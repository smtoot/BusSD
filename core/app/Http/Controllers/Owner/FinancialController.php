<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\Refund;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function transactions(Request $request)
    {
        $pageTitle = 'Transaction History';
        $owner = authUser();
        $remarks = Transaction::where('owner_id', $owner->id)->distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('owner_id', $owner->id)
            ->searchable(['trx'])
            ->filter(['remark'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('owner.financial.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function settlements()
    {
        $pageTitle = "Financial Settlements";
        $owner = authUser();
        $commissionRate = $owner->app_commission ?? gs('app_commission');

        $query = BookedTicket::where('owner_id', $owner->id)
            ->whereNotNull('passenger_id') // Only App
            ->active();

        // Summary Statistics
        $totalApp = (clone $query)->sum(\DB::raw('price * ticket_count'));
        $totalCommission = $totalApp * ($commissionRate / 100);
        $netEarnings = $totalApp - $totalCommission;

        $settlements = $query->with(['trip', 'trip.route'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.financial.settlements', compact('pageTitle', 'owner', 'settlements', 'totalApp', 'totalCommission', 'netEarnings', 'commissionRate'));
    }

    public function refunds()
    {
        $pageTitle = "App Refund Logs";
        $owner = authUser();

        $refunds = Refund::whereHas('bookedTicket', function($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            })
            ->with(['bookedTicket', 'bookedTicket.trip', 'passenger'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.financial.refunds', compact('pageTitle', 'refunds'));
    }
}
