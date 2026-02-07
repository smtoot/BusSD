<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\WithdrawalMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function withdrawMoney()
    {
        $pageTitle = 'Withdraw Money';
        $methods = WithdrawalMethod::active()->get();
        return view('owner.withdraw.methods', compact('pageTitle', 'methods'));
    }

    public function withdrawStore(Request $request)
    {
        $request->validate([
            'method_code' => 'required',
            'amount'      => 'required|numeric'
        ]);

        $method = WithdrawalMethod::active()->where('id', $request->method_code)->firstOrFail();
        $owner = authUser('owner');

        if ($request->amount < $method->min_limit || $request->amount > $method->max_limit) {
            $notify[] = ['error', 'Please follow the withdrawal limit'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $owner->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdrawal.'];
            return back()->withNotify($notify);
        }

        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $request->amount;

        $withdraw                     = new Withdrawal();
        $withdraw->method_id         = $method->id;
        $withdraw->owner_id          = $owner->id;
        $withdraw->amount            = $request->amount;
        $withdraw->currency          = gs('cur_text');
        $withdraw->rate              = 1;
        $withdraw->charge            = $charge;
        $withdraw->after_charge      = $afterCharge;
        $withdraw->final_amount      = $finalAmount;
        $withdraw->trx               = getTrx();
        $withdraw->status            = 0;
        $withdraw->save();

        // Debit Owner Balance
        $owner->balance -= $request->amount;
        $owner->save();

        $transaction               = new Transaction();
        $transaction->owner_id     = $owner->id;
        $transaction->amount       = $request->amount;
        $transaction->post_balance = $owner->balance;
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Withdraw Request Via ' . $method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw';
        $transaction->save();

        $notify[] = ['success', 'Withdrawal request submitted successfully'];
        return to_route('owner.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $pageTitle = 'Withdraw Log';
        $withdraws = Withdrawal::where('owner_id', authUser('owner')->id)->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('owner.withdraw.log', compact('pageTitle', 'withdraws'));
    }
}
