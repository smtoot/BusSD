# Phase 5: Post-Booking (E-Tickets & Notifications) Execution Plan

This phase ensures the passenger has a seamless digital experience after their payment is successful.

---

## 1. Digital E-Tickets
**Objective**: Generate a mobile-friendly ticket with a QR code.

### Technical Tasks:
1.  **API Endpoint**: `GET /api/v1/tickets/{id}/view`.
2.  **Logic**:
    - Fetch the `BookedTicket` with all route details.
    - Generate a unique QR data string (e.g., `booking_trx`).
3.  **Response**: JSON containing the passenger name, seat numbers, departure time, and QR code URL.

---

## 2. Trip History & Management
**Objective**: Allow passengers to view their upcoming and past journeys.

### Technical Tasks:
1.  **Upcoming Trips**: `GET /api/v1/passenger/trips/upcoming` (status = 1, date >= today).
2.  **Past Trips**: `GET /api/v1/passenger/trips/history` (date < today).
3.  **Cancellation Logic**: Allow users to cancel a "Pending" booking to free up seats immediately.

---

## 3. Real-Time Notifications
**Objective**: Alert passengers when their ticket is confirmed or if there's a delay.

### Technical Tasks:
1.  **Webhook Trigger**: Upon payment success, send a "Ticket Confirmed" SMS/Email.
2.  **Notification Drivers**: Utilize the existing `UserNotify` trait already implemented in the `Passenger` model.
3.  **Sudan Regionality**: Prioritize SMS notifications because of low internet penetration in rural bus stops.

---

## 4. Phase 5 Acceptance Criteria
- [ ] Passenger can see their booking in the "My Trips" section of the app.
- [ ] Clicking a trip opens a "Digital Ticket" with a scannable QR code.
- [ ] System sends a confirmation SMS immediately after payment.
