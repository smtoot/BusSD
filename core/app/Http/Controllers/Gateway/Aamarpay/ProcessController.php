<?php

namespace App\Http\Controllers\Gateway\Aamarpay;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessController extends Controller {

    public static function process($deposit) {
        $alias      = $deposit->gateway->alias;
        $credential = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $owner      = authUser();

        $data['store_id']      = $credential->store_id;
        $data['signature_key'] = $credential->signature_key;

        $data['tran_id']      = $deposit->trx;

        $data['success_url'] = route('ipn.' . $alias);
        $data['fail_url']    = route('ipn.' . $alias);
        $data['cancel_url']  = route('owner.dashboard');

        $data['amount']                     = round($deposit->final_amount);
        $data['amount_vatratio']            = 0;
        $data['amount_vat']                 = 0;
        $data['amount_taxratio']            = 0;
        $data['amount_tax']                 = 0;
        $data['amount_processingfee_ratio'] = 0;
        $data['amount_processingfee']       = 0;
        $data['currency']                   = $deposit->method_currency;

        $data['cus_name']     = $owner->owner_name;
        $data['cus_email']    = $owner->email;
        $data['cus_phone']    = $owner->mobileNumber;
        $data['cus_add1']     = @$owner->address;
        $data['cus_city']     = @$owner->city ?? "";
        $data['cus_state']    = @$owner->state ?? "";
        $data['cus_postcode'] = @$owner->zip ?? '';
        $data['cus_country']  = @$owner->country_name ?? "";

        $data['desc'] = "Deposit via Aamarpay";

        $apiBaseUrl = "https://secure.aamarpay.com";
        // $apiBaseUrl = "https://sandbox.aamarpay.com";

        try {
            $response = CurlRequest::curlPostContent($apiBaseUrl . "/request.php", $data);
            $send['redirect']     = true;
            $send['redirect_url'] = str_replace(array('\\', '"'), '', $apiBaseUrl . $response);
        } catch (Exception $ex) {
            $send['error']     = true;
            $send['message']   = "Payment processing error";
        }

        return json_encode($send);
    }


    public function ipn(Request $request) {
        $deposit         = Deposit::where('trx', $request->mer_txnid)->where('status',Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->first();
        if ($deposit) {
            Auth::guard('owner')->loginUsingId($deposit->owner_id);
            $credential      = json_decode($deposit->gatewayCurrency()->gateway_parameter);
            $deposit->detail = $request->all();
            $deposit->save();

            if (strtoupper($request->pay_status) == "SUCCESSFUL" && $credential->store_id == $request->store_id) {
                PaymentController::userDataUpdate($deposit);
                $notify[] = ['success', 'Transaction was successful'];
                return redirect($deposit->success_url)->withNotify($notify);
            }
        }
        $notify[] = ['error', "Transaction was failed"];
        return redirect($deposit->failed_url)->withNotify($notify);
    }
}
