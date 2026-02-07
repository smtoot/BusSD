# B2C Migration Extension: Strategic Roadmap (Phases 6-9)

Following the successful implementation of the B2C core, we are now addressing the "Business Pillars" required for a sustainable, multi-party SaaS ecosystem in Sudan.

---

## Phase 6: Operator Withdrawal System (The Financial Pillar)
**Goal**: Allow operators to legally and technically "cash out" their earnings.
- **Backend**: Create `withdrawals` and `withdrawal_methods` tables.
- **Operator Dashboard**: Logic to view "Withdrawable Balance" (B2C sales minus commission) and submit requests.
- **Admin Dashboard**: Verification and fulfillment flow for operator payouts.
- **Verification**: End-to-end payout from app sale to approved withdrawal.

## Phase 7: Ticket Cancellation & Refunds (The Trust Pillar)
**Goal**: Manage failed journeys and user change-of-mind without manual intervention.
- **Business Logic**: Configurable "Refund Policy" (e.g., 80% refund if >24h, 0% if <2h).
- **Passenger API**: "Request Refund" endpoint.
- **Automation**: Automatic balance credit or manual admin approval flow.
- **Verification**: Testing refund calculations against different time-to-journey scenarios.

## Phase 8: B2C Performance Analytics (The Operational Pillar)
**Goal**: Clear distinction between App sales and Counter sales.
- **Operator View**: Dedicated "B2C Sales" report showing passenger details and net earnings.
- **Admin View**: Commission aggregation report (How much the platform earned across all operators).
- **Verification**: Data accuracy check between transactions and reports.

## Phase 9: Quality Control & Ratings (The Brand Pillar)
**Goal**: Maintain high service standards for the TransLab brand.
- **Frontend/Mobile**: Post-trip 5-star rating and comment system.
- **Backend**: Aggregation logic to show average operator ratings.
- **Admin**: "Leaderboard" of top-rated operators vs. warnings for low-rated ones.
- **Verification**: Mock feedback flow and rating update validation.

---

**Current Status**: Ready to begin **Phase 6: Operator Withdrawal System**.
