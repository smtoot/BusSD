<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function pending()
    {
        $pageTitle = 'Pending Refunds';
        $refunds = Refund::where('status', 0)->with('passenger', 'bookedTicket', 'bookedTicket.trip')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.refund.list', compact('pageTitle', 'refunds'));
    }

    public function approved()
    {
        $pageTitle = 'Approved Refunds';
        $refunds = Refund::where('status', 1)->with('passenger', 'bookedTicket')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.refund.list', compact('pageTitle', 'refunds'));
    }

    public function rejected()
    {
        $pageTitle = 'Rejected Refunds';
        $refunds = Refund::where('status', 2)->with('passenger', 'bookedTicket')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.refund.list', compact('pageTitle', 'refunds'));
    }

    public function detail($id)
    {
        $refund = Refund::with('passenger', 'bookedTicket', 'bookedTicket.trip', 'bookedTicket.trip.owner')->findOrFail($id);
        $pageTitle = 'Refund Detail';
        return view('admin.refund.detail', compact('pageTitle', 'refund'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $refund = Refund::where('id', $request->id)->where('status', 0)->firstOrFail();
        
        $refund->status = 1;
        $refund->admin_feedback = $request->details;
        $refund->save();

        // Credit Passenger Wallet (Logic for Sudan SaaS)
        $passenger = $refund->passenger;
        // In this system, passengers don't have a balance column yet. 
        // For now, we log the transaction and assume manual payout or future wallet system.
        // If we want to credit operator (since they received the money), we might need to debit them.
        
        $bookedTicket = $refund->bookedTicket;
        $owner = $bookedTicket->trip->owner;

        // Debit the Operator because they were credited for the sale, but now it's refunded
        $owner->balance -= $refund->amount;
        $owner->save();

        $transaction               = new Transaction();
        $transaction->owner_id     = $owner->id;
        $transaction->amount       = $refund->amount;
        $transaction->post_balance = $owner->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Ticket Refund (Booked ID: ' . $bookedTicket->id . ')';
        $transaction->trx          = $refund->trx;
        $transaction->remark       = 'ticket_refund';
        $transaction->save();

        $notify[] = ['success', 'Refund approved and operator balance debited successfully'];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'details' => 'required']);
        $refund = Refund::where('id', $request->id)->where('status', 0)->firstOrFail();
        
        $refund->status = 2;
        $refund->admin_feedback = $request->details;
        $refund->save();

        // Restore ticket status back to active? 
        // No, if they requested refund, the seat was freed. They should re-book.
        // But the seat was freed in cancelTicket. If we reject refund, the ticket stays status 3.
        
        $notify[] = ['success', 'Refund request rejected'];
        return back()->withNotify($notify);
    }
}
