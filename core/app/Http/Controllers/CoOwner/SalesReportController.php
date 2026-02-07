<?php

namespace App\Http\Controllers\CoOwner;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\CounterManager;
use App\Models\Route;
use App\Models\Trip;

class SalesReportController extends Controller
{
    public function index()
    {
        $pageTitle = "All Sales";
        $owner = authUser('co-owner')->owner;

        $sales = BookedTicket::where('owner_id', $owner->id)
            ->filter(['trip:route_id', 'trip_id'])
            ->dateFilter()
            ->customDateFilter(['date_of_journey'])
            ->active()
            ->with('trip', 'trip.route', 'counterManager')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $routes = Route::active()->where('owner_id', $owner->id)->get();
        $trips = Trip::active()->where('owner_id', $owner->id)->get();
        $counterManagers = CounterManager::active()->where('owner_id', $owner->id)->with('counter')->get();
        return view('co_owner.report.sale', compact('pageTitle', 'sales', 'routes', 'trips', 'counterManagers', 'owner'));
    }

    public function saleDetail($id)
    {
        $pageTitle = "Booking Details";
        $owner = authUser('co-owner')->owner;
        $sale = $owner->bookedTickets()->active()->with('trip', 'trip.route', 'counterManager')->findOrFail($id);
        return view('co_owner.report.details', compact('pageTitle', 'sale', 'owner'));
    }
}
