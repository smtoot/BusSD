<?php

namespace App\Http\Controllers\Api\Passenger;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Route;
use App\Models\Trip;
use App\Models\TripRating;
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
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'fleet_type_id' => 'nullable|integer',
            'departure_time_start' => 'nullable|date_format:H:i',
            'departure_time_end' => 'nullable|date_format:H:i',
            'sort_by' => 'nullable|in:price_asc,price_desc,time_asc,rating_desc',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $pickupId = $request->pickup_id;
        $destinationId = $request->destination_id;
        $searchDate = Carbon::parse($request->date)->format('Y-m-d');
        $formatDate = Carbon::parse($request->date)->format('m/d/Y');

        $query = Trip::active()
            ->where('date', $searchDate)
            ->whereHas('route', function($q) use ($pickupId, $destinationId) {
                $q->active()
                  ->where('stoppages', 'like', '%"id": ' . $pickupId . '%')
                  ->where('stoppages', 'like', '%"id": ' . $destinationId . '%');
            })
            ->with(['route', 'fleetType', 'schedule', 'owner', 'bookedTickets' => function($q) use ($formatDate) {
                $q->where('date_of_journey', $formatDate)->whereIn('status', [1, 2]); 
            }, 'seatLocks' => function($q) use ($formatDate) {
                $q->where('date_of_journey', $formatDate)->where('expires_at', '>', now());
            }]);

        // Filter by Fleet Type
        if ($request->fleet_type_id) {
            $query->where('fleet_type_id', $request->fleet_type_id);
        }

        // Filter by Departure Time
        if ($request->departure_time_start || $request->departure_time_end) {
            $query->whereHas('schedule', function($q) use ($request) {
                if ($request->departure_time_start) {
                    $q->where('starts_from', '>=', $request->departure_time_start);
                }
                if ($request->departure_time_end) {
                    $q->where('starts_from', '<=', $request->departure_time_end);
                }
            });
        }

        $trips = $query->get();
        $results = [];
        foreach ($trips as $trip) {
            $stoppages = $trip->route->stoppages;
            
            // Extract IDs if it's an array of objects
            $stoppageIds = array_map(function($item) {
                return is_array($item) ? (string)$item['id'] : (string)$item;
            }, $stoppages);

            $pickupIndex = array_search((string)$pickupId, $stoppageIds);
            $destinationIndex = array_search((string)$destinationId, $stoppageIds);

            if ($pickupIndex === false || $destinationIndex === false || $pickupIndex >= $destinationIndex) {
                continue;
            }

            // Calculate Availability
            $totalSeats = (int) $trip->fleetType->total_seat;
            $bookedSeatsCount = (int) $trip->bookedTickets->sum('ticket_count');
            $lockedSeatsCount = 0;
            foreach ($trip->seatLocks as $lock) {
                $lockedSeatsCount += count($lock->seats);
            }
            $availableSeats = max(0, $totalSeats - ($bookedSeatsCount + $lockedSeatsCount));

            // Fetch Price (Segment based) - Priority to trip base_price if template/instance has it
            $fare = 0;
            if ($trip->base_price > 0) {
                $fare = (float) $trip->base_price;
            } elseif($trip->route->ticketPrice) {
                $priceRecord = $trip->route->ticketPrice->prices()
                    ->whereJsonContains('source_destination', [(string)$pickupId, (string)$destinationId])
                    ->first();
                $fare = $priceRecord ? (float) $priceRecord->price : 0;
            }

            if ($fare <= 0) continue; 

            // Filter by Price Range
            if ($request->min_price && $fare < $request->min_price) continue;
            if ($request->max_price && $fare > $request->max_price) continue;

            // Rating logic - Aggregate for owner
            $ownerRating = TripRating::whereHas('trip', function($q) use ($trip) {
                $q->where('owner_id', $trip->owner_id);
            })->avg('rating') ?: 0;

            $results[] = [
                'trip_id' => (int) $trip->id,
                'owner_name' => (string) ($trip->owner->general_settings->company_name ?? ($trip->owner->lastname ?: $trip->owner->username)), 
                'owner_logo' => $trip->owner->image ? url(getFilePath('ownerProfile') . '/' . $trip->owner->image) : null,
                'owner_rating' => round($ownerRating, 1),
                'bus_type' => (string) ($trip->bus_type ?: $trip->fleetType->name),
                'departure_time' => (string) $trip->schedule->starts_from,
                'arrival_time' => (string) $trip->schedule->ends_at,
                'fare' => (float) $fare,
                'available_seats' => (int) $availableSeats,
                'route_name' => (string) $trip->route->name,
                'raw_time' => $trip->schedule->starts_from, // used for sorting
            ];
        }

        // Sorting
        if ($request->sort_by) {
            $resultsCollection = collect($results);
            if ($request->sort_by == 'price_asc') {
                $results = $resultsCollection->sortBy('fare')->values()->all();
            } elseif ($request->sort_by == 'price_desc') {
                $results = $resultsCollection->sortByDesc('fare')->values()->all();
            } elseif ($request->sort_by == 'time_asc') {
                $results = $resultsCollection->sortBy('raw_time')->values()->all();
            } elseif ($request->sort_by == 'rating_desc') {
                $results = $resultsCollection->sortByDesc('owner_rating')->values()->all();
            }
        }

        // Clean up raw_time before response
        foreach ($results as &$res) {
            unset($res['raw_time']);
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
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $formatDate = Carbon::parse($request->date)->format('m/d/Y');

        // Fetch all booked seats (confirmed or pending payment)
        $bookedSeats = $trip->bookedTickets()
            ->where('date_of_journey', $formatDate)
            ->whereIn('status', [1, 2])
            ->get()
            ->pluck('seats')
            ->flatten()
            ->toArray();

        $lockedSeats = is_array($trip->app_locked_seats) ? $trip->app_locked_seats : [];
        $bookedSeats = array_values(array_unique(array_merge($bookedSeats, $lockedSeats)));

        $layout = $trip->fleetType->seatLayout;
        $seatsConfig = $trip->fleetType->seats; 

        $results = [
            'trip_id' => (int) $trip->id,
            'bus_name' => (string) $trip->title,
            'layout' => [
                'name' => (string) $layout->name,
                'total_seats' => (int) $trip->fleetType->total_seat,
                'deck' => (int) $trip->fleetType->deck,
                'schema' => $layout->schema ? (object) $layout->schema : null,
            ],
            'seats' => $seatsConfig,
            'booked_seats' => $bookedSeats
        ];

        return $this->apiSuccess(null, $results);
    }
}
