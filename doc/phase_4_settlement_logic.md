# Phase 4: Operator Settlement & Commissions (Technical Specification)

This phase addresses the financial relationship between the B2C Platform and the Transportation Operators.

## 1. Inventory Integrity
- **Logic**: The system currently handles inventory deduction dynamically.
- **How it works**: When a `BookedTicket` is marked as `status = 1` (Sold), it is automatically excluded from the "Available Seats" count in `TripSearchController.php` and `layout` logic.
- **Improvement**: We will implement a "Sales Report" for Operators so they can see exactly which B2C seats were sold in real-time.

## 2. Operator Credit & Commission System
When a passenger pays $100 via the B2C app, the system must calculate the "Service Fee" using a hierarchical logic:

### Flexible Commission Logic:
1.  **Global Rate**: A default commission percentage (e.g., 10%) set in the Admin General Settings.
2.  **Operator Override**: A specific commission percentage set for an individual operator (e.g., Al-Junaid might be 7% while others are 10%).
3.  **Calculation**: `Share = Ticket Price * (1 - MAX(Operator_Rate, Global_Rate))` (or similar logic depending on preference).

### Proposed Database Changes:
- **`general_settings`**: Add `b2c_commission` column (decimal).
- **`owners`**: Add `b2c_commission` column (decimal, nullable). If NULL, the Global Rate is used.
- `owner_id` link in `transactions` to track credits.
- `commissions` table to log exactly how much was taken from each ticket.
- `withdrawals` system for Operators to get their money from the platform.

## 3. Financial Workflow
1.  **Payment Received**: Passenger pays 10,000 SDG.
2.  **Split Logic**:
    - System calculates Platform Fee (e.g., 500 SDG).
    - System calculates Operator Share (9,500 SDG).
3.  **Balance Update**: Operator's `balance` in the `owners` table is increased by 9,500 SDG.
4.  **Audit Trail**: A `transaction` is created for the Operator with remark `b2c_ticket_sale`.

## 4. Acceptance Criteria
- [ ] Operator dashboard shows "B2C Earnings" separately.
- [ ] Platform Admin can set different commission rates for different operators.
- [ ] Automatic balance updates upon successful B2C payment fulfillment.
