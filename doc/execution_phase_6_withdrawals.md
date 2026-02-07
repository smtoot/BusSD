# Phase 6: Operator Withdrawal System (Technical Specification)

The goal of Phase 6 is to provide a secure and transparent way for bus companies (Operators) to withdraw their earnings from B2C ticket sales.

---

## 1. Database Schema Design

### 1.1 `withdrawal_methods` Table
Stores available payout options (e.g., Bank Transfer, SyberPay, Office Pickup).
- `id`
- `name` (string)
- `image` (string/nullable)
- `min_limit` (decimal)
- `max_limit` (decimal)
- `delay` (string) - Expected time (e.g., "24 Hours")
- `fixed_charge` (decimal)
- `percent_charge` (decimal)
- `user_data` (text/json) - Defines required fields for the operator (e.g., Bank Account Number).
- `status` (tinyint) - 1: Active, 0: Inactive

### 1.2 `withdrawals` Table
Stores the actual payout requests.
- `id`
- `method_id`
- `owner_id` (The Operator)
- `amount` (decimal) - Net amount requested
- `charge` (decimal) - Fees for withdrawal
- `final_amount` (decimal) - Total deducted from balance
- `after_charge` (decimal) - Amount to be sent to user
- `currency` (string)
- `rate` (decimal)
- `trx` (string/unique)
- `withdraw_information` (text) - Details provided by the operator
- `admin_feedback` (text/nullable)
- `status` (tinyint) - 0: Pending, 1: Approved, 2: Rejected
- `created_at`, `updated_at`

---

## 2. The Operator Flow
1.  **Dashboard**: Operator sees "Withdrawable Balance".
2.  **Withdraw Page**: List of active `withdrawal_methods`.
3.  **Initiate**: Operator selects method and enters amount.
4.  **Verification**: System checks if `amount + charge <= balance`.
5.  **Submission**: Balance is immediately **debited** and status set to `Pending`.

## 3. The Admin Flow
1.  **Payout Requests**: Admin sees a list of all `Pending` withdrawals.
2.  **Detail View**: Admin reviews the operator's provided information.
3.  **Approval**: Admin marks as `Approved` (and manually transfers money via their bank/app).
4.  **Rejection**: Admin marks as `Rejected` and provides feedback; the amount is **refunded** to the operator's balance.

---

## 4. Implementation Steps
- [ ] Migration: Create `withdrawal_methods` and `withdrawals` tables.
- [ ] Admin: Controller & Views to manage Withdrawal Methods.
- [ ] Operator: Controller & Views to initiate Withdraw requests.
- [ ] Admin: Controller & Views to Approve/Reject requests.
- [ ] Verification: End-to-end test of the payout cycle.
