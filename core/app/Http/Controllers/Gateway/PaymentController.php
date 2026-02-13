<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Owner;
use App\Models\SoldPackage;
use App\Models\Transaction;
use App\Models\BookedTicket;
use App\Models\Passenger;
use App\Models\SeatLock;
use App\Models\BranchRevenue;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        $pageTitle = 'Payment Methods';
        $order = SoldPackage::where('order_number', session('order_number'))->first();

        if (!$order) {
            $notify[] = ['error', 'Invalid Request'];
            return back()->withNotify($notify);
        }

        return view('owner.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'order'));
    }


    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'currency' => 'required',
        ]);

        $owner   = authUser();
        $order  = SoldPackage::where('order_number', session('order_number'))->first();
        if (!$order) {
            $notify[] = ['error', 'Invalid Request'];
            return back()->withNotify($notify);
        }

        if ($order->payment_status == Status::SOLD_PACKAGE_ACTIVE) {
            $notify[] = ['error', 'You have already paid for this order'];
            return to_route('owner.dashboard')->withNotify($notify);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($order->price != $request->amount) {
            $notify[] = ['error', 'The amount does not match the order price'];
            return back()->withNotify($notify);
        }

        $amount = $order->price;

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow payment limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data = new Deposit();
        $data->owner_id = $owner->id;
        $data->sold_package_id = $order->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $finalAmount;
        $data->btc_amount = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->success_url = route('owner.package.active');
        $data->failed_url = route('owner.package.index');
        $data->save();

        session()->put('Track', $data->trx);
        return to_route('owner.deposit.confirm');
    }

    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('owner.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($data->view, compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        $methodName = $deposit->methodName();

        if ($deposit->booked_ticket_id) {
                $booking = BookedTicket::with('trip', 'trip.owner')->find($deposit->booked_ticket_id);
                $booking->status = 1; // Sold
                $booking->save();

                // Release the seat lock immediately after successful payment
                SeatLock::where('trip_id', $booking->trip_id)
                    ->where('passenger_id', $booking->passenger_id)
                    ->where('date_of_journey', $booking->date_of_journey)
                    ->delete();

                $passenger = Passenger::find($deposit->passenger_id);
                $owner = $booking->trip->owner;
                $gs = gs();

                // Commission Logic: Operator Override > Global Rate
                $commissionRate = $owner->app_commission ?? $gs->app_commission;
                $commissionAmount = ($deposit->amount * $commissionRate) / 100;
                $operatorAmount = $deposit->amount - $commissionAmount;

                // Credit Operator
                $owner->balance += $operatorAmount;
                $owner->save();

                // Passenger Transaction
                $transaction               = new Transaction();
                $transaction->passenger_id = $deposit->passenger_id;
                $transaction->amount       = $deposit->amount;
                $transaction->post_balance = 0; // Passengers don't have a balance in the current schema
                $transaction->charge       = $deposit->charge;
                $transaction->trx_type     = '+';
                $transaction->details      = 'Ticket Payment Via ' . $methodName;
                $transaction->trx          = $deposit->trx;
                $transaction->remark       = 'ticket_payment';
                $transaction->save();

                // Operator Transaction (Credit for App Sale)
                $ownerTransaction               = new Transaction();
                $ownerTransaction->owner_id     = $owner->id;
                $ownerTransaction->amount       = $operatorAmount;
                $ownerTransaction->post_balance = $owner->balance;
                $ownerTransaction->charge       = $commissionAmount; // Commission is the 'charge' for the operator
                $ownerTransaction->trx_type     = '+';
                $ownerTransaction->details      = 'Credit from App Sale (Commission: ' . $commissionRate . '%)';
                $ownerTransaction->trx          = $deposit->trx;
                $ownerTransaction->remark       = 'b2c_ticket_sale';
                $ownerTransaction->save();

                // ===== Branch Revenue Tracking =====
                if ($booking->trip && $booking->trip->owning_branch_id) {
                    $trip = $booking->trip;
                    $splitModel = $trip->revenue_split_model ?? 'owning_branch';
                    
                    // Determine revenue split based on model
                    if ($splitModel === 'owning_branch' || !$trip->origin_branch_id || !$trip->destination_branch_id) {
                        // Default: 100% to owning branch
                        BranchRevenue::create([
                            'branch_id' => $trip->owning_branch_id,
                            'trip_id' => $trip->id,
                            'booking_id' => $booking->id,
                            'date' => $booking->date_of_journey,
                            'total_amount' => $deposit->amount,
                            'discount_amount' => 0, // Future: implement discount tracking
                            'net_amount' => $operatorAmount, // After platform commission
                            'split_model' => 'owning_branch',
                            'split_percentage' => 100,
                        ]);
                    } elseif ($splitModel === 'origin_branch' && $trip->origin_branch_id) {
                        // 100% to origin branch
                        BranchRevenue::create([
                            'branch_id' => $trip->origin_branch_id,
                            'trip_id' => $trip->id,
                            'booking_id' => $booking->id,
                            'date' => $booking->date_of_journey,
                            'total_amount' => $deposit->amount,
                            'discount_amount' => 0,
                            'net_amount' => $operatorAmount,
                            'split_model' => 'origin_branch',
                            'split_percentage' => 100,
                        ]);
                    } elseif ($splitModel === 'split_50_50' && $trip->origin_branch_id && $trip->destination_branch_id) {
                        // 50% to origin, 50% to destination
                        $splitAmount = $operatorAmount / 2;
                        
                        // Origin branch: 50%
                        BranchRevenue::create([
                            'branch_id' => $trip->origin_branch_id,
                            'trip_id' => $trip->id,
                            'booking_id' => $booking->id,
                            'date' => $booking->date_of_journey,
                            'total_amount' => $deposit->amount,
                            'discount_amount' => 0,
                            'net_amount' => $splitAmount,
                            'split_model' => 'split_50_50',
                            'split_percentage' => 50,
                        ]);
                        
                        // Destination branch: 50%
                        BranchRevenue::create([
                            'branch_id' => $trip->destination_branch_id,
                            'trip_id' => $trip->id,
                            'booking_id' => $booking->id,
                            'date' => $booking->date_of_journey,
                            'total_amount' => $deposit->amount,
                            'discount_amount' => 0,
                            'net_amount' => $splitAmount,
                            'split_model' => 'split_50_50',
                            'split_percentage' => 50,
                        ]);
                    }
                    // Note: 'custom' split model not yet implemented - requires JSON parsing
                }
                // ===== End Branch Revenue Tracking =====

                notify($passenger, 'TICKET_COMPLETE', [
                    'method_name'     => $methodName,
                    'method_currency' => $deposit->method_currency,
                    'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                    'amount'          => showAmount($deposit->amount, currencyFormat: false),
                    'charge'          => showAmount($deposit->charge, currencyFormat: false),
                    'rate'            => showAmount($deposit->rate, currencyFormat: false),
                    'trx'             => $deposit->trx,
                ]);
                
                return;
            }

            if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $owner           = Owner::find($deposit->owner_id);
            $owner->balance += $deposit->amount;
            $owner->save();

            $methodName = $deposit->methodName();

            $transaction               = new Transaction();
            $transaction->owner_id     = $deposit->owner_id;
            $transaction->amount       = $deposit->amount;
            $transaction->post_balance = $owner->balance;
            $transaction->charge       = $deposit->charge;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Payment Via ' . $methodName;
            $transaction->trx          = $deposit->trx;
            $transaction->remark       = 'payment';
            $transaction->save();

            // $packages = SoldPackage::active()->where('owner_id', $owner->id)->get();
            // foreach ($packages as $package) {
            //     $package->owner_id = 0;
            //     $package->save();
            // }


            $order           = SoldPackage::where('id', $deposit->sold_package_id)->with('package')->first();
            $order->owner_id = $owner->id;
            $order->status   = Status::SOLD_PACKAGE_ACTIVE;
            $order->save();

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->owner_id  = $owner->id;
                $adminNotification->title     = 'Payment successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($owner, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'package'         => $order->package->name,
                'method_name'     => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                'amount'          => showAmount($deposit->amount, currencyFormat: false),
                'charge'          => showAmount($deposit->charge, currencyFormat: false),
                'rate'            => showAmount($deposit->rate, currencyFormat: false),
                'trx'             => $deposit->trx,
                'post_balance'    => showAmount($owner->balance)
            ]);
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Payment';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('owner.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification = new AdminNotification();
        $adminNotification->owner_id = $data->owner->id;
        $adminNotification->title = 'Payment request from ' . $data->owner->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'package'         => $data->soldPackage->package->name,
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'Your payment request has been taken'];
        return to_route('owner.deposit.history')->withNotify($notify);
    }
}
