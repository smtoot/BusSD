# Phase 5: Passenger Management & Post-Booking Spec

## 1. Overview
After a successful payment, the Passenger App must provide immediate value through e-tickets and real-time alerts. This phase handles the digital fulfillment of the booking.

---

## 2. API: E-Ticket Generation
Currently, tickets are rendered as HTML for printing. For the mobile app, we need a secure PDF or Mobile-View.

- **Option A (PDF)**: Use `barryvdh/laravel-dompdf` to convert the existing `manager.trip.ticket` view into a PDF and return it as a stream/download.
- **Option B (JSON Ticket)**: Return all ticket metadata (`trx`, `seats`, `trip_time`, `qr_code_data`) so Flutter can render a native "Card" UI.

### Proposed Endpoint:
- **GET `/api/v1/tickets/{trx}`**
- **Security**: Must check that the `passenger_id` on the ticket matches the authenticated user.

---

## 3. Real-Time Notifications (FCM)
The system already has a `DeviceToken` model. We will extend this to Passengers.

- **Events to Notify**:
  1. **Booking Confirmed**: Triggered immediately after successful payment.
  2. **Trip Reminder**: Sent 1 hour before departure.
  3. **Cancellation**: If the Owner/Admin cancels the trip.

### Implementation:
Reuse the `UserNotify` trait logic but swap the "Driver" to `Firebase` (FCM) for the mobile app.

---

## 4. Passenger Dashboard (Booking History)
- **Endpoint**: `GET /api/v1/passenger/bookings`
- **Output**: List of `BookedTicket` filtered by `passenger_id`.
- **States**: `Upcoming`, `Completed`, `Cancelled`.

---

## 5. Completion Criteria
1. **E-Ticket Accessible**: Passenger can view/download their ticket from the app.
2. **Push Notifications Received**: Firebase integration is verified for booking success.
3. **History Functional**: Past and future trips are correctly segmented in the passenger profile.
4. **Security Verified**: A passenger cannot view another passenger's ticket by guessing the Transaction ID.
