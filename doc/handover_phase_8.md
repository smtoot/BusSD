# Handover: Phase 7 (B2C Performance Analytics)

This document records the technical implementation of the reporting and analytics dashboard for the B2C platform.

---

## 1. Files Created/Modified
| File Path | Purpose |
| :--- | :--- |
| `core/app/Http/Controllers/Owner/SalesReportController.php` | Modified: Added `b2cSales` method. |
| `core/app/Http/Controllers/Admin/ReportController.php` | Modified: Added `b2cPerformance` method. |
| `core/resources/views/owner/report/b2c_sale.blade.php` | New: Operator analytics view. |
| `core/resources/views/admin/report/b2c_performance.blade.php` | New: Admin performance dashboard. |
| `core/routes/owner.php` | Modified: Registered B2C report route. |
| `core/routes/admin.php` | Modified: Registered platform performance route. |

---

## 2. Analytic Logic

### 2.1 Operator Perspective (Net Earnings)
- Filters `BookedTicket` where `passenger_id` is NOT NULL.
- Calculates **Net Credit** dynamically: `Gross - (Gross * CommissionRate)`.
- Provides summary widgets for `Gross Volume`, `App Passengers`, and `Net Revenue`.

### 2.2 Admin Perspective (Platform Revenue)
- Aggregates `Transaction` table where `remark` is `b2c_ticket_sale`.
- **Gross Volume**: Sum of `amount + charge`.
- **Platform Commission**: Sum of `charge`.
- Listing of all recent B2C transactions for granular auditing.

---

**Handover Status**: Phase 8 is live and verified.
**Next Reference**: Phase 9 (Quality Control).
