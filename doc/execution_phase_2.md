# Phase 2: Seat Selection & Booking Initiation (Implementation Plan)

This phase handles the transition from finding a trip to selecting a seat and preparing for payment.

---

## 1. Module: Trip Layout & Availability
**Objective**: Provide a dynamic "map" of the bus to the Flutter app.

### Technical Tasks:
1. **API Endpoint**: `GET /api/v1/trips/{id}/layout`.
2. **Logic**:
   - Fetch the `FleetType` and linked `SeatLayout`.
   - Fetch all `BookedTicket` for the `trip_id` and `date`.
   - Map existing bookings to the layout grid.
3. **Response**: Return rows, columns, and a list of seat objects with `label`, `status`, and `price`.

### Acceptance Criteria:
- [ ] API correctly identifies "Booked" vs "Available" seats based on existing tickets.
- [ ] API returns correct row/column dimensions for different bus types (AC Deluxe vs Standard).
- [ ] Layout matches the configuration set in the Owner/Manager dashboard.

---

## 2. Module: Booking Initiation
**Objective**: Securely lock seats and create a pending record.

### Technical Tasks:
1. **API Endpoint**: `POST /api/v1/booking/initiate`.
2. **Atomic Logic**: Use `DB::transaction` to check seat availability one last time before creating the record.
3. **Record Creation**:
   - Create a `BookedTicket` record with `status = 0` (Pending).
   - Link it to the authenticated `passenger_identity`.
4. **Sudan Context**: Calculate the sub-segment price and display it clearly for the passenger.

### Acceptance Criteria:
- [ ] System prevents "Double Booking" (Two users selecting the same seat simultaneously).
- [ ] Rejects the request if the passenger is banned or account is unverified.
- [ ] Returns a unique Transaction ID (`trx`) for use in the Payment phase.

---

## 3. Phase 2 Definition of Done (DoD)
Phase 2 is complete when:
1. **Flutter** can display a 40-seat bus grid dynamically.
2. **Seats** change state (Available → Selected) in the app.
3. **Clicking Book** creates a "Pending" record in the TransLab database.
