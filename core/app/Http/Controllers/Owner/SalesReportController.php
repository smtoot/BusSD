<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\CounterManager;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Branch;
use App\Constants\Status;

class SalesReportController extends Controller
{
    public function index()
    {
        $pageTitle = "All Sales";
        $owner = authUser();

        $sales = BookedTicket::where('owner_id', $owner->id)
            ->filter(['trip:route_id', 'trip_id'])
            ->dateFilter()
            ->customDateFilter(['date_of_journey'])
            ->active()
            ->with('trip', 'trip.route', 'trip.owningBranch', 'counterManager')
            ->when(request('branch_id'), function($q) {
                $q->whereHas('trip', function($query) {
                    $query->where('owning_branch_id', request('branch_id'));
                });
            })
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $routes = Route::active()->where('owner_id', $owner->id)->get();
        $trips = Trip::active()->where('owner_id', $owner->id)->get();
        $counterManagers = CounterManager::active()->where('owner_id', $owner->id)->with('counter')->get();
        $branches = Branch::where('owner_id', $owner->id)->orderBy('name')->get();
        return view('owner.report.sale', compact('pageTitle', 'sales', 'routes', 'trips', 'counterManagers', 'branches', 'owner'));
    }

    public function saleDetail($id)
    {
        $pageTitle = "Booking Details";
        $owner = authUser();
        $sale = $owner->bookedTickets()->active()->with('trip', 'trip.route', 'counterManager')->findOrFail($id);
        return view('owner.report.details', compact('pageTitle', 'sale', 'owner'));
    }

    public function b2cSales()
    {
        $pageTitle = "B2C (App) Sales";
        $owner = authUser();

        $query = BookedTicket::where('owner_id', $owner->id)
            ->whereNotNull('passenger_id'); // Filter: Only B2C Passengers

        // Apply trip filter
        if (request()->filled('trip_id')) {
            $query->where('trip_id', request('trip_id'));
        }

        // Apply status filter
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        } else {
            // Default: only show active bookings if no status filter
            $query->active();
        }

        // Apply date range filter
        if (request()->filled('date')) {
            $dates = explode(' - ', request('date'));
            if (count($dates) == 2) {
                $query->whereDate('date_of_journey', '>=', trim($dates[0]))
                      ->whereDate('date_of_journey', '<=', trim($dates[1]));
            } elseif (count($dates) == 1) {
                $query->whereDate('date_of_journey', trim($dates[0]));
            }
        }

        // Apply branch filter
        if (request()->filled('branch_id')) {
            $query->whereHas('trip', function($q) {
                $q->where('owning_branch_id', request('branch_id'));
            });
        }

        $sales = $query->with('trip', 'trip.route', 'trip.owningBranch', 'passenger')
            ->orderByDesc('id')
            ->paginate(getPaginate())
            ->appends(request()->all());

        // Get all trips for filter dropdown
        $trips = Trip::active()->where('owner_id', $owner->id)->orderBy('title')->get();
        $branches = Branch::where('owner_id', $owner->id)->orderBy('name')->get();

        return view('owner.report.b2c_sale', compact('pageTitle', 'sales', 'owner', 'trips', 'branches'));
    }

    public function counterSales()
    {
        $pageTitle = "Counter (Office) Sales";
        $owner = authUser();

        $query = BookedTicket::where('owner_id', $owner->id)
            ->whereNull('passenger_id'); // Filter: Only Counter/Manual Bookings

        // Apply trip filter
        if (request()->filled('trip_id')) {
            $query->where('trip_id', request('trip_id'));
        }

        // Apply status filter
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        } else {
            // Default: only show active bookings if no status filter
            $query->active();
        }

        // Apply date range filter
        if (request()->filled('date')) {
            $dates = explode(' - ', request('date'));
            if (count($dates) == 2) {
                $query->whereDate('date_of_journey', '>=', trim($dates[0]))
                      ->whereDate('date_of_journey', '<=', trim($dates[1]));
            } elseif (count($dates) == 1) {
                $query->whereDate('date_of_journey', trim($dates[0]));
            }
        }

        // Apply branch filter
        if (request()->filled('branch_id')) {
            $query->whereHas('trip', function($q) {
                $q->where('owning_branch_id', request('branch_id'));
            });
        }

        $sales = $query->with('trip', 'trip.route', 'trip.owningBranch', 'counterManager')
            ->orderByDesc('id')
            ->paginate(getPaginate())
            ->appends(request()->all());

        // Get all trips for filter dropdown
        $trips = Trip::active()->where('owner_id', $owner->id)->orderBy('title')->get();
        $branches = Branch::where('owner_id', $owner->id)->orderBy('name')->get();

        return view('owner.report.counter_sale', compact('pageTitle', 'sales', 'owner', 'trips', 'branches'));
    }

    public function periodic()
    {
        $pageTitle = "Periodic Sales Report";
        $owner = authUser();

        $sales = BookedTicket::where('owner_id', $owner->id)
            ->active()
            ->customDateFilter(['date_of_journey'])
            ->when(request('branch_id'), function($q) {
                $q->whereHas('trip', function($query) {
                    $query->where('owning_branch_id', request('branch_id'));
                });
            })
            ->with(['trip', 'trip.route', 'trip.owningBranch', 'counterManager', 'passenger'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $branches = Branch::where('owner_id', $owner->id)->orderBy('name')->get();
        return view('owner.report.periodic', compact('pageTitle', 'sales', 'branches', 'owner'));
    }

    public function performance()
    {
        $pageTitle = "Route & Trip Performance";
        $owner = authUser();

        $routePerformance = BookedTicket::where('booked_tickets.owner_id', $owner->id)
            ->where('booked_tickets.status', Status::ENABLE)
            ->join('trips', 'booked_tickets.trip_id', '=', 'trips.id')
            ->join('routes', 'trips.route_id', '=', 'routes.id')
            ->selectRaw('routes.name as route_name, count(booked_tickets.id) as total_bookings, sum(booked_tickets.ticket_count * booked_tickets.price) as total_revenue')
            ->groupBy('routes.id', 'routes.name')
            ->orderByDesc('total_revenue')
            ->get();

        $tripPerformance = BookedTicket::where('booked_tickets.owner_id', $owner->id)
            ->where('booked_tickets.status', Status::ENABLE)
            ->join('trips', 'booked_tickets.trip_id', '=', 'trips.id')
            ->selectRaw('trips.title as trip_title, count(booked_tickets.id) as total_bookings, sum(booked_tickets.ticket_count * booked_tickets.price) as total_revenue')
            ->groupBy('trips.id', 'trips.title')
            ->orderByDesc('total_revenue')
            ->get();

        return view('owner.report.performance', compact('pageTitle', 'routePerformance', 'tripPerformance'));
    }
}
