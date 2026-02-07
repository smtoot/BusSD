# Phase 1.2: Trip Search API Implementation Spec

## 1. Overview
The Trip Search API is the "Front Door" of the B2C Passenger App. It must allow passengers to find available buses between two locations on a specific date, across all participating transport companies.

---

## 2. API Endpoint Definition
- **Endpoint**: `GET /api/v1/trips/search`
- **Authentication**: Optional (allows guest searching).
- **Parameters**: 
  - `pickup_id` (Integer, Required): The ID of the starting counter.
  - `destination_id` (Integer, Required): The ID of the ending counter.
  - `date` (String, Required): Format `YYYY-MM-DD`.

---

## 3. Search Logic & Optimization
The traditional web logic is Owner-centric. The B2C API must be **Platform-centric**.

### 3.1 Selection Query
```php
$trips = Trip::active()
    ->whereJsonDoesntContain('day_off', $dayOfWeek)
    ->whereHas('route', function($query) use ($pickup_id, $destination_id) {
        $query->active()
              ->whereJsonContains('stoppages', $pickup_id)
              ->whereJsonContains('stoppages', $destination_id);
    })
    ->with(['route', 'fleetType', 'schedule', 'owner'])
    ->get();
```

### 3.2 Directional Validation (Critical)
Since `whereJsonContains` doesn't enforce order, the API must filter results where the passenger's `pickup_id` appears **before** the `destination_id` in the `route->stoppages` array.

---

## 4. Price Calculation
Pricing in TransLab is stored per segment in the `ticket_prices` table.
- **Logic**: The API must look up the price in the `TicketPriceByStoppage` model where the `source_destination` JSON field matches `[pickup_id, destination_id]`.
- **Handling Missing Fares**: If no specific price is set for the sub-segment, the trip should be hidden from search results to prevent "zero-fare" errors.

---

## 5. JSON Response Structure (Flutter Ready)
```json
{
  "status": "success",
  "data": [
    {
      "trip_id": 101,
      "company_name": "Royal Express",
      "bus_type": "AC Deluxe (2x2)",
      "departure": "08:30 AM",
      "arrival": "02:15 PM",
      "fare": 25.00,
      "available_seats": 12,
      "route": "London to Manchester"
    }
  ]
}
```

---

## 6. Completion Criteria
1. **Endpoint Functional**: Responds to pickup/destination queries with valid JSON.
2. **Direction Aware**: Returns zero results if the destination is "behind" the pickup on the route.
3. **Availability Calculated**: `booked_tickets` for the specific date are subtracted from total capacity to show real-time "available_seats".
4. **Multi-Owner Support**: Returns trips from all active "Owners" in the platform.
