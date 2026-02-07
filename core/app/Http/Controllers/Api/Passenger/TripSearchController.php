<?php

namespace App\Http\Controllers\Api\Passenger;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Route;
use App\Models\Trip;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TripSearchController extends Controller
{
    use ApiResponse;

    public function locations()
    {
        $locations = Counter::active()->orderBy('name')->get(['id', 'name', 'location']);

        return $this->apiSuccess(null, $locations);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_id' => 'required|integer',
            'destination_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $pickupId = $request->pickup_id;
        $destinationId = $request->destination_id;
        $date = Carbon::parse($request->date)->format('m/d/Y');
        $dayOfWeek = Carbon::parse($request->date)->format('l');

        $trips = Trip::active()
            ->whereJsonDoesntContain('day_off', $dayOfWeek)
            ->whereHas('route', function($q) use ($pickupId, $destinationId) {
                $q->active()
                  ->whereJsonContains('stoppages', (string)$pickupId)
                  ->whereJsonContains('stoppages', (string)$destinationId);
            })
            ->with(['route', 'fleetType', 'schedule', 'owner', 'bookedTickets' => function($q) use ($date) {
                $q->where('date_of_journey', $date)->where('status', 1);
            }])
            ->get();

        $results = [];

        foreach ($trips as $trip) {
            $stoppages = $trip->route->stoppages;
            $pickupIndex = array_search((string)$pickupId, $stoppages);
            $destinationIndex = array_search((string)$destinationId, $stoppages);

            // Directional Validation
            if ($pickupIndex === false || $destinationIndex === false || $pickupIndex >= $destinationIndex) {
                continue;
            }

            // Calculate Availability
            $totalSeats = $trip->fleetType->total_seat;
            $bookedSeatsCount = $trip->bookedTickets->sum('ticket_count');
            $lockedSeatsCount = is_array($trip->b2c_locked_seats) ? count($trip->b2c_locked_seats) : 0;
            $availableSeats = max(0, $totalSeats - ($bookedSeatsCount + $lockedSeatsCount));

            // Fetch Price (Simplified for now - usually needs more complex segment lookup)
            // In a real implementation, we'd query TicketPriceByStoppage
            $fare = 0;
            if($trip->route->ticketPrice) {
                $priceRecord = $trip->route->ticketPrice->prices()
                    ->whereJsonContains('source_destination', [(string)$pickupId, (string)$destinationId])
                    ->first();
                $fare = $priceRecord ? $priceRecord->price : 0;
            }

            if ($fare <= 0) continue; // Skip if no price is set for this segment

            $results[] = [
                'trip_id' => $trip->id,
                'owner_name' => $trip->owner->lastname, // Assuming company name is in lastname or similar
                'bus_type' => $trip->fleetType->name,
                'departure_time' => $trip->schedule->start_from,
                'arrival_time' => $trip->schedule->end_at,
                'fare' => $fare,
                'available_seats' => $availableSeats,
                'route_name' => $trip->route->name,
            ];
        }

        return $this->apiSuccess(null, $results);
    }

    public function layout(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'pickup_id' => 'required|integer',
            'destination_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $trip = Trip::active()->with(['fleetType', 'fleetType.seatLayout'])->findOrFail($id);
        $date = Carbon::parse($request->date)->format('m/d/Y');

        // Fetch all booked seats for this trip and date
        $bookedSeats = $trip->bookedTickets()
            ->where('date_of_journey', $date)
            ->where('status', 1)
            ->get()
            ->pluck('seats')
            ->flatten()
            ->toArray();

        $lockedSeats = is_array($trip->b2c_locked_seats) ? $trip->b2c_locked_seats : [];
        $bookedSeats = array_values(array_unique(array_merge($bookedSeats, $lockedSeats)));

        $layout = $trip->fleetType->seatLayout;
        $seatsConfig = $trip->fleetType->seats; // This is the object containing total rows, cols, etc.

        $results = [
            'trip_id' => $trip->id,
            'bus_name' => $trip->title,
            'layout' => [
                'name' => $layout->name,
                'total_seats' => $trip->fleetType->total_seat,
                'deck' => $trip->fleetType->deck,
            ],
            'seats' => $seatsConfig,
            'booked_seats' => $bookedSeats
        ];

        return $this->apiSuccess(null, $results);
    }
}
