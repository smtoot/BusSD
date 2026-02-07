<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Models\BookedTicket;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function index()
    {
        $pageTitle = "My Passengers";
        $owner = authUser();

        $passengers = Passenger::whereHas('bookedTickets', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })
            ->when(request()->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('firstname', 'like', "%$search%")
                      ->orWhere('lastname', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('mobile', 'like', "%$search%");
                });
            })
            ->withCount(['bookedTickets' => function($query) use ($owner) {
                $query->where('owner_id', $owner->id)->active();
            }])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.passenger.index', compact('pageTitle', 'passengers'));
    }

    public function viewHistory($id)
    {
        $pageTitle = "Passenger Booking History";
        $owner = authUser();
        $passenger = Passenger::whereHas('bookedTickets', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })->findOrFail($id);

        $bookings = BookedTicket::where('passenger_id', $passenger->id)
            ->where('owner_id', $owner->id)
            ->with(['trip', 'trip.route', 'trip.schedule'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.passenger.history', compact('pageTitle', 'passenger', 'bookings'));
    }
}
