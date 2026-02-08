<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\Owner;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Bookings';
        $bookings = $this->bookingData();
        return view('admin.bookings.index', compact('pageTitle', 'bookings'));
    }

    public function b2cBookings()
    {
        $pageTitle = 'B2C Bookings';
        $bookings = $this->bookingData('b2c');
        return view('admin.bookings.index', compact('pageTitle', 'bookings'));
    }

    public function counterBookings()
    {
        $pageTitle = 'Counter Bookings';
        $bookings = $this->bookingData('counter');
        return view('admin.bookings.index', compact('pageTitle', 'bookings'));
    }

    protected function bookingData($scope = null)
    {
        $query = BookedTicket::query()->with(['owner', 'trip', 'trip.route', 'passenger', 'counterManager', 'counterManager.counter']);
        
        if ($scope == 'b2c') {
            $query->whereNotNull('passenger_id');
        } elseif ($scope == 'counter') {
            $query->whereNull('passenger_id');
        }

        return $query->searchable(['trx', 'passenger:email', 'counterManager:username'])
            ->filter(['owner_id', 'trip_id', 'status'])
            ->orderByDesc('id')
            ->paginate(getPaginate());
    }

    public function show($id)
    {
        $booking = BookedTicket::with(['owner', 'trip', 'trip.route', 'trip.fleetType', 'passenger', 'counterManager', 'counterManager.counter'])->findOrFail($id);
        $pageTitle = 'Booking Detail - ' . $booking->trx;
        return view('admin.bookings.show', compact('pageTitle', 'booking'));
    }

    public function export()
    {
        $bookings = BookedTicket::with(['owner', 'trip', 'passenger', 'counterManager'])->get();
        $filename = 'bookings_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['PNR', 'Operator', 'Trip', 'Passenger/Counter', 'Seats', 'Amount', 'Date', 'Status']);
            foreach ($bookings as $booking) {
                $user = $booking->passenger ? ($booking->passenger->firstname . ' ' . $booking->passenger->lastname) : (@$booking->counterManager->fullname . ' (Counter)');
                fputcsv($file, [
                    $booking->trx,
                    @$booking->owner->username,
                    @$booking->trip->title,
                    $user,
                    is_array($booking->seats) ? implode(', ', $booking->seats) : $booking->seats,
                    $booking->price,
                    $booking->created_at,
                    $booking->status == 1 ? 'Confirmed' : ($booking->status == 3 ? 'Cancelled' : 'Pending')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
