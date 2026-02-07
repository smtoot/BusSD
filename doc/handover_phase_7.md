# Handover: Phase 7 (Ticket Cancellation & Refunds)

This document records the technical implementation of the automated refund request and cancellation system for B2C passengers.

---

## 1. Files Created/Modified
| File Path | Purpose |
| :--- | :--- |
| `core/database/migrations/2026_02_06_164053_create_refunds_table.php` | New: Refund tracking infrastructure. |
| `core/app/Models/Refund.php` | New: Model for tracking refund status and passenger info. |
| `core/app/Http/Controllers/Api/Passenger/BookingController.php` | Modified: Added `cancelTicket` API with policy math. |
| `core/app/Http/Controllers/Admin/RefundController.php` | New: Dashboard logic for approving/rejecting refunds. |
| `core/routes/api.php` | Modified: Exposed cancellation API. |
| `core/routes/admin.php` | Modified: Exposed refund management routes. |

---

## 2. Refund Policy Implementation
The logic is hardcoded (but easily configurable) in `BookingController::cancelTicket`:
- **> 24 hours**: 90% Refund
- **12 - 24 hours**: 70% Refund
- **2 - 12 hours**: 50% Refund
- **< 2 hours**: Cancellation forbidden.

---

## 3. Financial Reconcilliation
When an Admin **Approves** a refund:
1.  The Operator's balance is **debited** (since they were credited during the sale).
2.  A `Transaction` is logged as `ticket_refund`.
3.  The seat remains "Status 3" (Cancelled), freeing it up for the next passenger.

---

**Handover Status**: Phase 7 is live and verified.
**Next Reference**: Phase 8 (B2C Specialized Reporting).
