# Handover: Phase 4 (Operator Settlement & Commissions)

This document records the technical changes made to implement the financial relationship between the platform and operators.

---

## 1. Files Created/Modified
| File Path | Purpose |
| :--- | :--- |
| `core/app/Http/Controllers/Gateway/PaymentController.php` | Modified: Implemented automatic credit and commission calculation. |
| `core/app/Http/Controllers/Admin/GeneralSettingController.php` | Modified: Exposed global commission setting to Admin. |
| `core/app/Http/Controllers/Admin/ManageUsersController.php` | Modified: Exposed per-operator commission override to Admin. |
| `core/resources/views/admin/setting/general.blade.php` | Modified: Added Global Commission UI field. |
| `core/resources/views/admin/users/detail.blade.php` | Modified: Added Operator Override UI field. |
| `core/database/migrations/2026_02_06_161416_add_commission_fields.php` | New: Added columns for commission rates. |

---

## 2. Technical Logic

### 2.1 Hierarchical Commissions
The system now calculates fees using a "Fallback" pattern:
- **Level 1 (Override)**: If the `owners.b2c_commission` field is not null, it uses that specific rate.
- **Level 2 (Global)**: If no override exists, it defaults to the `general_settings.b2c_commission` rate.

### 2.2 Automatic Crediting
When a B2C ticket is paid:
1.  System calculates the split (e.g., 10% Platform, 90% Operator).
2.  The Operator's `balance` is increased immediately.
3.  A `Transaction` is logged with remark `b2c_ticket_sale`, including the exact commission percentage as the "charge".

### 2.3 Accounting Integrity
- **Passenger**: Sees $100 payment.
- **Operator**: Sees $90 credit + $10 charge (commission).
- **Platform**: Retains the commission in the centralized wallet.

---

**Handover Status**: Financial settlement infrastructure is live.
**Next Reference**: Phase 6.1 (Production Readiness & Scaling).
