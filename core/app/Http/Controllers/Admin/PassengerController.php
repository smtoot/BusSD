<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Models\BookedTicket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PassengerController extends Controller
{
    public function index()
    {
        $pageTitle = __("Manage Passengers");

        $passengers = Passenger::query()
            ->withCount(['bookedTickets as total_bookings' => function($q) {
                $q->where('status', 1); // Only confirmed bookings
            }])
            ->withSum(['bookedTickets as total_spent' => function($q) {
                $q->where('status', 1);
            }], 'price')
            ->when(request('search'), function($query) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when(request('status') !== null, function($query) {
                $query->where('status', request('status'));
            })
            ->when(request('date'), function($query) {
                $dates = explode(' - ', request('date'));
                if (count($dates) == 2) {
                    $query->whereDate('created_at', '>=', trim($dates[0]))
                          ->whereDate('created_at', '<=', trim($dates[1]));
                }
            })
            ->orderByDesc('id')
            ->paginate(getPaginate())
            ->appends(request()->all());

        // Get statistics for dashboard cards
        $totalPassengers = Passenger::count();
        $activePassengers = Passenger::where('status', 1)->count();
        $bannedPassengers = Passenger::where('status', 0)->count();
        $newThisMonth = Passenger::whereMonth('created_at', Carbon::now()->month)
                                  ->whereYear('created_at', Carbon::now()->year)
                                  ->count();

        return view('admin.passengers.index', compact(
            'pageTitle',
            'passengers',
            'totalPassengers',
            'activePassengers',
            'bannedPassengers',
            'newThisMonth'
        ));
    }

    public function show($id)
    {
        $pageTitle = __("Passenger Details");
        $passenger = Passenger::with(['bookedTickets' => function($q) {
            $q->with('trip', 'trip.route')->orderByDesc('id');
        }])->findOrFail($id);

        // Calculate statistics
        $totalBookings = $passenger->bookedTickets()->where('status', 1)->count();
        $totalSpent = $passenger->bookedTickets()->where('status', 1)->sum('price');
        $cancelledBookings = $passenger->bookedTickets()->where('status', 3)->count();
        $upcomingTrips = $passenger->bookedTickets()
                                    ->where('status', 1)
                                    ->where('date_of_journey', '>', Carbon::now())
                                    ->count();

        return view('admin.passengers.show', compact(
            'pageTitle',
            'passenger',
            'totalBookings',
            'totalSpent',
            'cancelledBookings',
            'upcomingTrips'
        ));
    }

    public function ban($id)
    {
        $passenger = Passenger::findOrFail($id);
        $passenger->status = 0;
        $passenger->save();

        $notify[] = ['success', __('Passenger banned successfully')];
        return back()->withNotify($notify);
    }

    public function unban($id)
    {
        $passenger = Passenger::findOrFail($id);
        $passenger->status = 1;
        $passenger->save();

        $notify[] = ['success', __('Passenger unbanned successfully')];
        return back()->withNotify($notify);
    }

    public function export()
    {
        $passengers = Passenger::query()
            ->withCount(['bookedTickets as total_bookings' => function($q) {
                $q->where('status', 1);
            }])
            ->withSum(['bookedTickets as total_spent' => function($q) {
                $q->where('status', 1);
            }], 'price')
            ->when(request('search'), function($query) {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->when(request('status') !== null, function($query) {
                $query->where('status', request('status'));
            })
            ->orderByDesc('id')
            ->get();

        $filename = 'passengers_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($passengers) {
            $file = fopen('php://output', 'w');

            // Add header row
            fputcsv($file, [
                __('ID'),
                __('Name'),
                __('Email'),
                __('Mobile'),
                __('Total Bookings'),
                __('Total Spent'),
                __('Status'),
                __('Registered Date')
            ]);

            // Add data rows
            foreach ($passengers as $passenger) {
                fputcsv($file, [
                    $passenger->id,
                    $passenger->firstname . ' ' . $passenger->lastname,
                    $passenger->email,
                    $passenger->mobile,
                    $passenger->total_bookings ?? 0,
                    number_format($passenger->total_spent ?? 0, 2),
                    $passenger->status ? __('Active') : __('Banned'),
                    $passenger->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
