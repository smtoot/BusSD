# Handover: Phase 3 (Mobile Payment Integration)

This document records the technical changes made to implement the Mobile Payment Gateway Bridge.

---

## 1. Files Created/Modified
| File Path | Purpose |
| :--- | :--- |
| `core/app/Http/Controllers/Api/Passenger/PaymentController.php` | New: Unified mobile payment entry point. |
| `core/app/Http/Controllers/Gateway/PaymentController.php` | Modified: Added logic to fulfill `BookedTicket` orders. |
| `core/app/Models/Deposit.php` | Modified: Linked deposits to passengers and bookings. |
| `core/app/Models/BookedTicket.php` | Modified: Added relationship to the Passenger model. |
| `core/app/Models/Transaction.php` | Modified: (via DB) Added `passenger_id` column. |
| `core/database/migrations/2026_02_06_155453_add_passenger_fields_to_deposits.php` | New: Schema update for deposits. |
| `core/database/migrations/2026_02_06_155453_add_passenger_id_to_transactions.php` | New: Schema update for transactions. |
| `core/routes/api.php` | Modified: Registered payment initiation and method list endpoints. |

---

## 2. Technical Logic

### 2.1 Unified Initiation
- **Endpoint**: `POST /api/v1/payment/initiate`
- **Feature**: Mobile app sends `booking_id` and `method_code`. The API creates a standard `Deposit` record and returns the necessary gateway data (e.g., Stripe Redirect URL or BOK Manual instructions).

### 2.2 Global Fulfillment Bridge
- I modified the system's core `userDataUpdate` (the function called by Stripe/PayPal once money arrives).
- **New Logic**: If the deposit is linked to a `booked_ticket_id`, the system now automatically marks the seat as **Sold (Status 1)** and notifies the passenger.

### 2.3 Sudanese localization
- The system now supports a "BOK Manual receipt" flow. If a user doesn't have a card, they can pay via Bank of Khartoum, upload the receipt to the Admin, and the Admin can approve it just like they already do for owners.

---

**Handover Status**: Payment infrastructure is live and integrated.
**Next Reference**: Phase 5 (E-Tickets & Notifications).
