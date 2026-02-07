# Phase 2.1: Seat Selection & Booking Initiation Spec

## 1. Overview
This phase handles the transition from finding a trip to selecting a specific seat. It requires the API to provide a "Visual Map" of the bus and a secure way to hold those seats during the checkout process.

---

## 2. API: Trip Layout & Seat Availability
- **Endpoint**: `GET /api/v1/trips/{id}/layout`
- **Parameters**: `date` (Required, YYYY-MM-DD), `pickup_id`, `destination_id`.
- **Logic**:
  1. Fetch the `FleetType` associated with the trip.
  2. Retrieve the `SeatLayout` (Rows, Columns, Seats per row).
  3. Fetch all `BookedTicket` records for this `trip_id` and `date`.
  4. Compare the sub-segment (`pickup` to `destination`) with existing bookings to see which seats are truly occupied for this specific leg of the journey.

### JSON Response Mapping (for Flutter `GridView`):
```json
{
  "layout": {
    "rows": 10,
    "columns": 4,
    "aisle_after_column": 2,
    "total_seats": 40
  },
  "seats": [
    {"label": "A1", "status": "available", "price": 25.0},
    {"label": "A2", "status": "booked", "price": 25.0},
    {"label": "A3", "status": "available", "price": 25.0}
  ]
}
```

---

## 3. API: Booking Initiation
- **Endpoint**: `POST /api/v1/booking/initiate`
- **Authentication**: Required (Passenger Bearer Token).
- **Body**:
  ```json
  {
    "trip_id": 101,
    "date": "2026-02-15",
    "pickup_id": 5,
    "destination_id": 12,
    "seats": ["A1", "A3"],
    "passenger_details": {
       "name": "John Doe",
       "gender": "male",
       "age": 30
    }
  }
  ```

---

## 4. The "Race Condition" Shield
In a B2C app, two users might click the same seat at the same time.
- **Implementation**: Before returning a success for `initiate`, the backend MUST perform a database transaction check:
  ```php
  DB::transaction(function () use ($requestedSeats) {
      $alreadyBooked = BookedTicket::where('trip_id', $id)
          ->where('date_of_journey', $date)
          ->whereJsonContains('seats', $requestedSeats)
          ->exists();
      if ($alreadyBooked) throw new Exception("Seats already taken");
      // Create Pending Booking...
  });
  ```

---

## 5. Completion Criteria
1. **Layout API**: Accurately reflects the bus grid and current occupancy.
2. **Atomic Booking**: Successfully prevents double-booking via DB transactions.
3. **Pending State**: Booking is created with `status = 0` (Pending) until payment is confirmed in Phase 3.
4. **Validation**: API rejects bookings if the date is in the past or the sub-segment is invalid.
