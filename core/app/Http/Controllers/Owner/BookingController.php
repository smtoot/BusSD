<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\BookedTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Bookings Overview";
        $owner = authUser();
        
        $date = $request->date ? Carbon::parse($request->date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        
        $trips = Trip::where('owner_id', $owner->id)
            ->whereDate('departure_datetime', $date)
            ->with(['fleetType', 'route', 'schedule', 'bookedTickets'])
            ->orderBy('departure_datetime', 'asc')
            ->get();
            
        return view('owner.bookings.index', compact('pageTitle', 'trips', 'date'));
    }

    public function manage($id)
    {
        $owner = authUser();
        $trip = Trip::where('owner_id', $owner->id)
            ->with(['fleetType', 'route', 'schedule', 'bookedTickets', 'bookedTickets.passenger'])
            ->findOrFail($id);
            
        $pageTitle = "Manage Trip: " . $trip->title;
        
        // Flatten passengers list from booked tickets
        $passengers = [];
        foreach ($trip->bookedTickets as $ticket) {
            foreach ($ticket->seats as $seat) {
                $passengers[] = (object)[
                    'seat_no' => $seat,
                    'name' => $ticket->passenger_details['name'],
                    'phone' => $ticket->passenger_details['mobile_number'] ?? 'N/A',
                    'booking_id' => '#' . sprintf('%06d', $ticket->id),
                    'source' => $ticket->passenger_id ? 'App' : 'Counter',
                    'is_boarded' => $ticket->is_boarded,
                    'ticket_id' => $ticket->id
                ];
            }
        }
        
        // Sort by seat number
        usort($passengers, function($a, $b) {
            return strnatcmp($a->seat_no, $b->seat_no);
        });

        return view('owner.bookings.manage', compact('pageTitle', 'trip', 'passengers'));
    }

    public function checkin(Request $request, $id)
    {
        $owner = authUser();
        $ticket = BookedTicket::where('owner_id', $owner->id)->findOrFail($id);
        
        $ticket->is_boarded = $request->status ? 1 : 0;
        $ticket->boarded_at = $request->status ? Carbon::now() : null;
        $ticket->save();
        
        return response()->json([
            'success' => true,
            'message' => $request->status ? 'Passenger checked-in' : 'Check-in canceled',
            'boarded_count' => $ticket->trip->boardedCount(),
            'booked_count' => $ticket->trip->bookedCount(),
            'progress' => $ticket->trip->checkinProgress()
        ]);
    }
}
