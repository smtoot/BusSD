<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Settlement;
use App\Models\BookedTicket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SettlementController extends Controller
{
    /**
     * Display a listing of settlements.
     */
    public function index()
    {
        $pageTitle = 'Settlements';
        $settlements = Settlement::with('owner')->latest()->paginate(getPaginate());
        return view('admin.settlement.index', compact('pageTitle', 'settlements'));
    }

    /**
     * Process a new settlement for an operator.
     */
    public function process(Request $request)
    {
        $request->validate([
            'owner_id' => 'required|exists:owners,id',
            'start_date' => 'required|date|after_or_equal:2020-01-01|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
        ]);

        $owner = Owner::findOrFail($request->owner_id);
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Calculate Gross Volume for App bookings
        $grossAmount = BookedTicket::where('owner_id', $owner->id)
            ->whereNotNull('passenger_id')
            ->where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(\DB::raw('price * ticket_count'));

        if ($grossAmount <= 0) {
            $notify[] = ['error', 'No App sales found for this operator in the specified period.'];
            return back()->withNotify($notify);
        }

        $commissionRate = $owner->app_commission ?? gs('app_commission');
        $commissionAmount = ($grossAmount * $commissionRate) / 100;
        $netAmount = $grossAmount - $commissionAmount;

        $settlement = new Settlement();
        $settlement->owner_id = $owner->id;
        $settlement->gross_amount = $grossAmount;
        $settlement->commission_amount = $commissionAmount;
        $settlement->net_amount = $netAmount;
        $settlement->status = 0; // Pending Payout
        $settlement->trx = getTrx();
        $settlement->settlement_period = $request->start_date . ' - ' . $request->end_date;
        $settlement->save();

        $notify[] = ['success', 'Settlement generated successfully for ' . $owner->fullname];
        return back()->withNotify($notify);
    }

    /**
     * Mark a settlement as paid.
     */
    public function markAsPaid($id)
    {
        $settlement = Settlement::findOrFail($id);
        if ($settlement->status == 1) {
            $notify[] = ['error', 'This settlement is already paid.'];
            return back()->withNotify($notify);
        }

        $settlement->status = 1; // Paid
        $settlement->save();

        $notify[] = ['success', 'Settlement marked as paid successfully.'];
        return back()->withNotify($notify);
    }
}
