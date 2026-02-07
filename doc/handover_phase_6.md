# Handover: Phase 6 (Operator Withdrawal System)

This document records the technical implementation of the payout system for bus operators.

---

## 1. Files Created/Modified
| File Path | Purpose |
| :--- | :--- |
| `core/database/migrations/2026_02_06_163112_create_withdrawals_table.php` | New: Payout infrastructure tables. |
| `core/app/Models/Withdrawal.php` | New: Request and tracking model. |
| `core/app/Models/WithdrawalMethod.php` | New: Payout channel model (Bank, etc.). |
| `core/app/Http/Controllers/Admin/WithdrawalController.php` | New: Admin approval/rejection logic. |
| `core/app/Http/Controllers/Admin/WithdrawalMethodController.php` | New: Payout method management. |
| `core/app/Http/Controllers/Owner/WithdrawController.php` | New: Operator request interface. |
| `core/routes/admin.php` | Modified: Registered withdrawal routes. |
| `core/routes/owner.php` | Modified: Registered payout routes. |

---

## 2. Payout Lifecycle

### 2.1 The Request (Debit-First)
- When an operator requests **$50,000 SDG**, their balance is immediately deducted from the `owners` table.
- A `Transaction` of type `-` is logged as a "Withdraw Request".
- The request sits in the `withdrawals` table with `status: 0` (Pending).

### 2.2 The Approval
- Once the Admin verifies the bank transfer manually, they hit "Approve" in the dashboard.
- The status changes to `status: 1` (Approved).

### 2.3 The Rejection (Refund)
- If the request is invalid, the Admin hits "Reject".
- The system **automatically refunds** the money to the Operator's balance.
- A `Transaction` of type `+` is logged as "Withdrawal Rejected - Refunded".

---

**Handover Status**: Phase 6 core logic is live.
**Next Reference**: Phase 7 (Cancellations & Refunds).
