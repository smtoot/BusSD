<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\Refund;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function settlements()
    {
        $pageTitle = "Financial Settlements";
        $owner = authUser();
        $commissionRate = $owner->b2c_commission ?? gs('b2c_commission');

        $query = BookedTicket::where('owner_id', $owner->id)
            ->whereNotNull('passenger_id') // Only B2C
            ->active();

        // Summary Statistics
        $totalB2C = (clone $query)->sum(\DB::raw('price * ticket_count'));
        $totalCommission = $totalB2C * ($commissionRate / 100);
        $netEarnings = $totalB2C - $totalCommission;

        $settlements = $query->with(['trip', 'trip.route'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.financial.settlements', compact('pageTitle', 'owner', 'settlements', 'totalB2C', 'totalCommission', 'netEarnings', 'commissionRate'));
    }

    public function refunds()
    {
        $pageTitle = "B2C Refund Logs";
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
