<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function pending()
    {
        $pageTitle = 'Pending Withdrawals';
        $withdrawals = Withdrawal::where('status', 0)->with('owner', 'method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Withdrawals';
        $withdrawals = Withdrawal::where('status', 1)->with('owner', 'method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Withdrawals';
        $withdrawals = Withdrawal::where('status', 2)->with('owner', 'method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function log()
    {
        $pageTitle = 'All Withdrawals';
        $withdrawals = Withdrawal::with('owner', 'method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function detail($id)
    {
        $withdrawal = Withdrawal::where('id', $id)->with('owner', 'method')->firstOrFail();
        $pageTitle = 'Withdrawal Detail';
        return view('admin.withdraw.detail', compact('pageTitle', 'withdrawal'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id', $request->id)->where('status', 0)->firstOrFail();
        $withdraw->status = 1;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        $notify[] = ['success', 'Withdrawal approved successfully'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'details' => 'required']);
        $withdraw = Withdrawal::where('id', $request->id)->where('status', 0)->firstOrFail();
        
        $withdraw->status = 2;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        // Refund Operator Balance
        $owner = $withdraw->owner;
        $owner->balance += $withdraw->amount;
        $owner->save();

        $transaction               = new Transaction();
        $transaction->owner_id     = $owner->id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $owner->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Withdrawal Rejected - Refunded';
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw_refund';
        $transaction->save();

        $notify[] = ['success', 'Withdrawal rejected successfully'];
        return back()->withNotify($notify);
    }
}
