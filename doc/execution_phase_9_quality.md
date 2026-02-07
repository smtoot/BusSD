# Phase 9: Quality Control & Ratings (Technical Specification)

Phase 9 introduces the "Expansion Pillar," enabling a feedback loop between passengers and operators to ensure high service standards are maintained.

---

## 1. Feature: Trip Ratings & Reviews

Passengers can rate their experience on a scale of 1 to 5 and leave a text review.

### Business Rules:
- **Eligibility**: Only passengers with a "Confirmed" (`status: 1`) ticket for a journey that has already started/finished can rate.
- **Single Entry**: A passenger can only rate a specific trip booking once.
- **Visibility**: Ratings are visible to the Operator (to improve) and the Admin (to monitor quality).

---

## 2. Database Schema Design

### 2.1 `trip_ratings` Table
- `id`
- `booked_ticket_id` (FK to `booked_tickets`)
- `passenger_id` (FK to `passengers`)
- `trip_id` (FK to `trips`)
- `rating` (tinyint) - 1 to 5 stars.
- `comment` (text)
- `created_at`, `updated_at`

---

## 3. Passenger Flow (Mobile API)
1.  **Request**: Passenger calls `ticket/rate/{id}` with `rating` and `comment`.
2.  **Validation**:
    - Does the ticket belong to the passenger?
    - Is the ticket status "Confirmed"?
    - Is the journey date in the past or today?
    - Has this ticket been rated already?
3.  **Submission**: Create a `trip_ratings` record.

## 4. Operational Oversight (Dashboards)
1.  **Operator**: Sees a new "Feedbacks" section showing reviews for their buses.
2.  **Admin**: Can view global feedback patterns and identify low-performing operators.

---

## 5. Implementation Steps
- [ ] Migration: Create `trip_ratings` table.
- [ ] Model: Create `TripRating` with relationships.
- [ ] API: Implement `rateTrip` method in `BookingController`.
- [ ] Operator: View for reading and filtering trip reviews.
- [ ] Verification: Test rating constraints (e.g., prevent rating future trips).
