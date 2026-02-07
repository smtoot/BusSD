# Phase 7: Ticket Cancellation & Refunds (Technical Specification)

Phase 7 introduces a formal "Cancellation & Refund" mechanism to protect passenger rights while maintaining operational stability for bus companies.

---

## 1. Business Logic: The Refund Policy

To avoid financial losses for operators, refunds will be calculated based on the "Time Before Departure":

| Timing | Refund Percentage | Note |
| :--- | :--- | :--- |
| > 24 Hours Before | 90% | 10% administrative fee retained. |
| 12 to 24 Hours Before | 70% | |
| 2 to 12 Hours Before | 50% | |
| < 2 Hours Before | 0% | No refund allowed for last-minute cancellations. |

> [!NOTE]
> Fees like "Payment Gateway Charges" or "Platform Commission" are typically NOT refunded.

---

## 2. Database Schema Design

### 2.1 `refunds` Table
Tracks passenger refund requests for B2C tickets.
- `id`
- `booked_ticket_id` (FK to `booked_tickets`)
- `passenger_id` (FK to `passengers`)
- `amount` (decimal) - Requested refund amount
- `status` (tinyint) - 0: Pending, 1: Approved, 2: Rejected
- `admin_feedback` (text)
- `trx` (unique transaction code)
- `created_at`, `updated_at`

---

## 3. The Passenger Flow (Mobile API)
1.  **Request**: Passenger calls `ticket/cancel/{id}`.
2.  **Validation**: 
    - Is the journey starting in > 2 hours?
    - Is the ticket already used or cancelled?
3.  **Calculation**: System calculates the refund amount based on the policy table.
4.  **Submission**: A `refunds` record is created, and the ticket status is updated strictly to prevent double-spending/travel.

## 4. The Admin/Operator Flow (Admin Dashboard)
1.  **Review**: Admin sees the refund request and the calculated amount.
2.  **Approval**: Admin approves; money is returned to the Passenger's wallet or credited for manual bank transfer.
3.  **Operator Notification**: The seat is marked as "Vacant" in the inventory for resale.

---

## 5. Implementation Steps
- [ ] Migration: Create `refunds` table.
- [ ] API: Implement `cancelTicket` endpoint for passengers.
- [ ] Logic: Dynamic refund percentage calculator.
- [ ] Admin: Interface to manage refund requests.
- [ ] Verification: Test refund math against journey timestamps.
