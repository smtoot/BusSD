<?php

namespace App\Http\Controllers\Api\Passenger;

use App\Http\Controllers\Controller;
use App\Models\BookedTicket;
use App\Models\Trip;
use App\Models\Waitlist;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    use ApiResponse;

    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'pickup_id' => 'required|integer',
            'destination_id' => 'required|integer',
            'seats' => 'required|array|min:1',
            'passenger_details' => 'required|array|min:1',
            'passenger_details.*.name' => 'required|string|max:100',
            'passenger_details.*.mobile' => 'nullable|string|max:20',
            'passenger_details.*.gender' => 'nullable|in:male,female',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        if (count($request->passenger_details) !== count($request->seats)) {
            return $this->apiError('Passenger details count must match the number of seats.', 422);
        }

        $passenger = $request->user();
        if ($passenger->status == 0) {
            return $this->apiError('Account banned.', 403);
        }

        $trip = Trip::active()->with(['route'])->findOrFail($request->trip_id);
        $date = Carbon::parse($request->date)->format('m/d/Y');
        $requestedSeats = array_map('strval', $request->seats);

        // 1. Static B2C Exclusions (Counter only seats)
        $staticLocked = is_array($trip->b2c_locked_seats) ? $trip->b2c_locked_seats : [];
        foreach ($requestedSeats as $seat) {
            if (in_array((string)$seat, array_map('strval', $staticLocked))) {
                return $this->apiError("Seat $seat is reserved for counter booking only.", 400);
            }
        }

        // 2. Dynamic Seat Locking Check (In-progress checkouts)
        $alreadyLocked = \App\Models\SeatLock::where('trip_id', $trip->id)
            ->where('date_of_journey', $date)
            ->where('expires_at', '>', Carbon::now())
            ->where('passenger_id', '!=', $passenger->id) // Allow user to re-initiate their own lock
            ->where(function($query) use ($requestedSeats) {
                foreach ($requestedSeats as $seat) {
                    $query->orWhereJsonContains('seats', $seat);
                }
            })
            ->exists();

        if ($alreadyLocked) {
            return $this->apiError('One or more selected seats are temporarily reserved. Please try again in 10 minutes.', 400);
        }

        try {
            return DB::transaction(function () use ($trip, $date, $requestedSeats, $request, $passenger) {
                // 3. Atomic Final Booking Check
                $alreadyBooked = BookedTicket::where('trip_id', $trip->id)
                    ->where('date_of_journey', $date)
                    ->whereIn('status', [1, 2]) // Confirmed or Payment Pending
                    ->where(function($query) use ($requestedSeats) {
                        foreach ($requestedSeats as $seat) {
                            $query->orWhereJsonContains('seats', $seat);
                        }
                    })
                    ->exists();

                if ($alreadyBooked) {
                    return $this->apiError('One or more selected seats are already booked.', 400);
                }

                // 4. Create Dynamic Seat Lock
                $existingLock = \App\Models\SeatLock::where('trip_id', $trip->id)
                    ->where('passenger_id', $passenger->id)
                    ->where('date_of_journey', $date)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();

                if ($existingLock) {
                    return $this->apiError('You already have an active booking in progress. Please complete or cancel it first.', 400);
                }

                \App\Models\SeatLock::create([
                    'trip_id' => $trip->id,
                    'passenger_id' => $passenger->id,
                    'date_of_journey' => $date,
                    'seats' => $requestedSeats,
                    'expires_at' => Carbon::now()->addMinutes(10)
                ]);

                // 5. Create Pending Booking
                $booking = new BookedTicket();
                $booking->owner_id = $trip->owner_id;
                $booking->trip_id = $trip->id;
                $booking->passenger_id = $passenger->id;
                $booking->source_destination = [$request->pickup_id, $request->destination_id];
                $booking->pick_up_point = $request->pickup_id;
                $booking->dropping_point = $request->destination_id;
                $booking->seats = $requestedSeats;
                $booking->passenger_details = $request->passenger_details;
                $booking->ticket_count = count($requestedSeats);
                $booking->date_of_journey = $date;
                $booking->status = 0; // Pending
                $booking->trx = getTrx();

                // Price Calculation (Segment based)
                $priceRecord = $trip->route->ticketPrice->prices()
                    ->whereJsonContains('source_destination', [(string)$request->pickup_id, (string)$request->destination_id])
                    ->first();
                $booking->price = ($priceRecord ? $priceRecord->price : 0) * count($requestedSeats);

                if ($booking->price <= 0) {
                    throw new \Exception("Invalid price for this segment.");
                }

                $booking->save();

                return $this->apiSuccess('Booking initiated. Seats locked for 10 minutes. Please proceed to payment.', [
                    'trx' => $booking->trx,
                    'amount' => $booking->price,
                    'booking_id' => $booking->id,
                    'lock_expires_at' => Carbon::now()->addMinutes(10)->toDateTimeString()
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Booking initiation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->apiError('Something went wrong. Please try again.', 500);
        }
    }

    /**
     * Release locked seats manually (e.g. user cancels checkout)
     */
    public function releaseSeats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $date = Carbon::parse($request->date)->format('m/d/Y');
        
        \App\Models\SeatLock::where('trip_id', $request->trip_id)
            ->where('passenger_id', $request->user()->id)
            ->where('date_of_journey', $date)
            ->delete();

        // Trigger waitlist processing for this trip/date
        try {
            \App\Models\Waitlist::where('trip_id', $request->trip_id)
                ->where('date_of_journey', $date)
                ->where('status', 0)
                ->orderBy('created_at')
                ->take(1) // Notify top 1 for now
                ->get()
                ->each(function($entry) {
                    $entry->status = 1;
                    $entry->save();
                    // In real app, send push notification here
                });
        } catch (\Exception $e) {
            // Ignore waitlist errors during release
        }

        return $this->apiSuccess('Seats released successfully.');
    }

    public function joinWaitlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'pickup_id' => 'required|integer',
            'destination_id' => 'required|integer',
            'seat_count' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = $request->user();
        $date = Carbon::parse($request->date)->format('Y-m-d');

        // Check if already on waitlist
        $exists = Waitlist::where('passenger_id', $passenger->id)
            ->where('trip_id', $request->trip_id)
            ->where('date_of_journey', $date)
            ->whereIn('status', [0, 1]) // Pending or Notified
            ->exists();

        if ($exists) {
            return $this->apiError('You are already on the waitlist for this trip.', 400);
        }

        $waitlist = Waitlist::create([
            'passenger_id' => $passenger->id,
            'trip_id' => $request->trip_id,
            'date_of_journey' => $date,
            'pickup_id' => $request->pickup_id,
            'destination_id' => $request->destination_id,
            'seat_count' => $request->seat_count,
            'status' => 0 // Pending
        ]);

        return $this->apiSuccess('Joined waitlist successfully.', $waitlist);
    }

    public function checkWaitlistStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = $request->user();
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $entry = Waitlist::where('passenger_id', $passenger->id)
            ->where('trip_id', $request->trip_id)
            ->where('date_of_journey', $date)
            ->latest()
            ->first();

        if (!$entry) {
            return $this->apiSuccess('Not on waitlist', ['status' => 'none']);
        }

        return $this->apiSuccess('Waitlist status retrieved', ['status' => $entry->status, 'entry' => $entry]);
    }

    public function upcoming(Request $request)
    {
        $passenger = $request->user();
        $now = Carbon::now()->format('m/d/Y');
        $perPage = min((int) $request->input('per_page', 15), 50);

        $bookings = BookedTicket::where('passenger_id', $passenger->id)
            ->whereIn('status', [1, 2]) // Confirmed or Payment Pending
            ->where('date_of_journey', '>=', $now)
            ->with(['trip', 'trip.owner', 'trip.route', 'trip.schedule'])
            ->orderBy('date_of_journey', 'asc')
            ->paginate($perPage);

        // Map results for cleaner JSON
        $bookings->getCollection()->transform(function($item) {
            return [
                'id' => (int) $item->id,
                'trx' => (string) $item->trx,
                'trip_title' => (string) $item->trip->title,
                'owner' => (string) $item->trip->owner->lastname ?: $item->trip->owner->username,
                'route' => (string) $item->trip->route->name,
                'departure' => (string) $item->trip->schedule->start_from,
                'date' => (string) $item->date_of_journey,
                'seats' => $item->seats,
                'total_price' => (float) $item->price,
                'status' => (int) $item->status,
            ];
        });

        return $this->apiSuccess(null, $bookings);
    }

    public function history(Request $request)
    {
        $passenger = $request->user();
        $now = Carbon::now()->format('m/d/Y');
        $perPage = min((int) $request->input('per_page', 15), 50);

        $bookings = BookedTicket::where('passenger_id', $passenger->id)
            ->where('status', 1) // Only completed trips
            ->where('date_of_journey', '<', $now)
            ->with(['trip', 'trip.owner', 'trip.route', 'trip.schedule'])
            ->orderBy('date_of_journey', 'desc')
            ->paginate($perPage);

        $bookings->getCollection()->transform(function($item) {
            return [
                'id' => (int) $item->id,
                'trx' => (string) $item->trx,
                'trip_title' => (string) $item->trip->title,
                'owner' => (string) $item->trip->owner->lastname ?: $item->trip->owner->username,
                'route' => (string) $item->trip->route->name,
                'departure' => (string) $item->trip->schedule->start_from,
                'date' => (string) $item->date_of_journey,
                'seats' => $item->seats,
                'total_price' => (float) $item->price,
                'status' => (int) $item->status,
            ];
        });

        return $this->apiSuccess(null, $bookings);
    }

    public function viewTicket(Request $request, $id)
    {
        $passenger = $request->user();
        $ticket = BookedTicket::where('id', $id)
            ->where('passenger_id', $passenger->id)
            ->with(['trip', 'trip.owner', 'trip.route', 'trip.schedule', 'trip.fleetType'])
            ->firstOrFail();

        // QR Data for scanning
        $qrData = [
            'trx' => (string) $ticket->trx,
            'passenger' => (string) $passenger->firstname . ' ' . $passenger->lastname,
            'bus' => (string) $ticket->trip->title,
            'seats' => $ticket->seats,
            'date' => (string) $ticket->date_of_journey,
        ];

        return $this->apiSuccess(null, [
            'ticket' => [
                'id' => (int) $ticket->id,
                'trx' => (string) $ticket->trx,
                'trip_title' => (string) $ticket->trip->title,
                'owner' => (string) $ticket->trip->owner->lastname ?: $ticket->trip->owner->username,
                'route' => (string) $ticket->trip->route->name,
                'departure' => (string) $ticket->trip->schedule->start_from,
                'arrival' => (string) $ticket->trip->schedule->end_at,
                'date' => (string) $ticket->date_of_journey,
                'seats' => $ticket->seats,
                'passenger_details' => $ticket->passenger_details,
                'price' => (float) $ticket->price,
                'status' => (int) $ticket->status,
                'bus_type' => (string) $ticket->trip->fleetType->name,
            ],
            'qr_data' => $qrData
        ]);
    }

    public function cancelTicket(Request $request, $id)
    {
        $passenger = $request->user();
        $ticket = BookedTicket::where('id', $id)
            ->where('passenger_id', $passenger->id)
            ->where('status', 1) // Confirmed only
            ->with(['trip', 'trip.schedule'])
            ->firstOrFail();

        $journeyTime = Carbon::parse($ticket->date_of_journey . ' ' . $ticket->trip->schedule->start_from);
        $now = Carbon::now();
        $hoursBefore = $now->diffInHours($journeyTime, false);

        if ($hoursBefore < 2) {
            return $this->apiError('Cancellations are not allowed within 2 hours of departure.', 400);
        }

        // Refund Percentage Logic
        if ($hoursBefore > 24) {
            $refundPercent = 90;
        } elseif ($hoursBefore >= 12) {
            $refundPercent = 70;
        } else {
            $refundPercent = 50;
        }

        $refundAmount = ($ticket->price * $refundPercent) / 100;

        try {
            return DB::transaction(function () use ($ticket, $refundAmount, $refundPercent, $passenger) {
                // Mark ticket as cancelled IMMEDIATELY to free seat and prevent use
                $ticket->status = 3; // 3: Cancelled/Refund Pending
                $ticket->save();

                // Create Refund Request
                $refund = new \App\Models\Refund();
                $refund->booked_ticket_id = $ticket->id;
                $refund->passenger_id = $passenger->id;
                $refund->amount = $refundAmount;
                $refund->trx = $ticket->trx;
                $refund->status = 0; // Pending Admin Approval
                $refund->save();

                return $this->apiSuccess("Cancellation request submitted. Expected refund: {$refundAmount} " . gs('cur_text'), [
                    'refund_amount' => $refundAmount,
                    'refund_percent' => $refundPercent,
                    'ticket_id' => $ticket->id
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Ticket cancellation failed', ['error' => $e->getMessage(), 'ticket_id' => $id]);
            return $this->apiError('Something went wrong. Please try again.', 500);
        }
    }

    public function rateTrip(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        $passenger = $request->user();
        $ticket = BookedTicket::where('id', $id)
            ->where('passenger_id', $passenger->id)
            ->where('status', 1) // Confirmed only
            ->with(['trip', 'trip.schedule'])
            ->firstOrFail();

        $journeyTime = Carbon::parse($ticket->date_of_journey . ' ' . $ticket->trip->schedule->start_from);

        // Ensure journey has started or finished
        if (Carbon::now()->lessThan($journeyTime)) {
            return $this->apiError('You can only rate a trip after the journey has started.', 400);
        }

        // Check for duplicate rating
        $existing = \App\Models\TripRating::where('booked_ticket_id', $ticket->id)->exists();
        if ($existing) {
            return $this->apiError('You have already rated this trip.', 400);
        }

        $rating = new \App\Models\TripRating();
        $rating->booked_ticket_id = $ticket->id;
        $rating->passenger_id = $passenger->id;
        $rating->trip_id = $ticket->trip_id;
        $rating->rating = $request->rating;
        $rating->comment = $request->comment;
        $rating->save();

        return $this->apiSuccess('Thank you for your feedback!', $rating);
    }
}
