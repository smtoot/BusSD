<?php

namespace App\Http\Controllers\Api\Passenger;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use ApiResponse;

    public function methods()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        return $this->apiSuccess(null, $gatewayCurrency);
    }

    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:booked_tickets,id',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = $request->user();
        
        // Anti-Fraud/Security: Check banned status
        if ($passenger->status == 0) {
            return $this->apiError('Account banned. Please contact support.', 403);
        }

        $booking = BookedTicket::where('id', $request->booking_id)->where('passenger_id', $passenger->id)->firstOrFail();

        if ($booking->status == 1) {
            return $this->apiError('This ticket is already paid.', 400);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            return $this->apiError('Invalid gateway.', 400);
        }

        $amount = (float) $booking->price;
        if ((float)$gate->min_amount > $amount || (float)$gate->max_amount < $amount) {
            return $this->apiError('Please follow payment limit.', 400);
        }

        $charge = (float) $gate->fixed_charge + ($amount * (float) $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $finalAmount = $payable * (float) $gate->rate;

        $data = new Deposit();
        $data->passenger_id = $passenger->id;
        $data->booked_ticket_id = $booking->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = (float) $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = ""; // Handled by App
        $data->failed_url = ""; // Handled by App
        $data->save();

        if ($data->method_code >= 1000) {
            return $this->apiSuccess('Manual payment initiated.', [
                'trx' => (string) $data->trx,
                'type' => 'manual',
                'amount' => (float) $data->amount,
                'instructions' => (string) $gate->method->description
            ]);
        }

        // Automatic Gateway
        $dirName = $gate->method->alias;
        $new = 'App\\Http\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';

        $processData = $new::process($data);
        $processData = json_decode($processData);

        return $this->apiSuccess(null, [
            'trx' => (string) $data->trx,
            'type' => 'automatic',
            'amount' => (float) $data->amount,
            'process_data' => $processData
        ]);
    }

    public function manualPaymentConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trx' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = $request->user();
        if ($passenger->status == 0) {
            return $this->apiError('Account banned.', 403);
        }

        $deposit = Deposit::with('gateway', 'gateway.form')->where('trx', $request->trx)->where('status', Status::PAYMENT_INITIATE)->first();
        if (!$deposit) {
            return $this->apiError('Deposit not found or already processed.', 404);
        }

        $gateway = $deposit->gateway;
        $formData = $gateway->form->form_data;

        $formProcessor = new \App\Lib\FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);
        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $userData = $formProcessor->processFormData($request, $formData);

        $deposit->detail = $userData;
        $deposit->status = Status::PAYMENT_PENDING;
        $deposit->from_api = 1;
        $deposit->save();

        $adminNotification = new \App\Models\AdminNotification();
        $adminNotification->title = 'Manual payment request from Passenger via API';
        $adminNotification->click_url = urlPath('admin.deposit.details', $deposit->id);
        $adminNotification->passenger_id = $deposit->passenger_id;
        $adminNotification->save();

        return $this->apiSuccess('Your payment proof has been submitted and is pending approval.', [
            'trx' => (string) $deposit->trx,
            'status' => 'Pending',
            'amount' => (float) $deposit->amount
        ]);
    }
}
