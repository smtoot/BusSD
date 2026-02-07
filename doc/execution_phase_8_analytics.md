# Phase 8: B2C Performance Analytics (Technical Specification)

Phase 8 focuses on "Operational Transparency," providing clear insights into the success of the B2C platform for both the bus companies and the Super Admin.

---

## 1. Operator-Specific B2C Report
Operators need to justify the commission they pay by seeing the value the App brings them.

### Data Requirements:
- **Passenger Identity**: Name and Mobile from the `passengers` table.
- **Gross Sale**: Total ticket price paid by the user.
- **Commission Amount**: The actual fee deducted (based on global/override rate).
- **Net Credit**: Gross - Commission (What actually hit the operator's balance).
- **Booking Status**: Active vs. Refunded.

### UI Design:
- A new menu item: `B2C Sales Report`.
- Filtering by date, trip, and passenger.
- Summary widgets: `Total B2C Volume`, `Total App Passengers`, `Net Earnings`.

---

## 2. Admin Commission Dashboard
The Super Admin needs a high-level view of platform monetization.

### Data Requirements:
- **Total Gross Volume**: Sum of all B2C ticket sales.
- **Total Commission Earned**: Sum of `charge` from `Transactions` where remark is `b2c_ticket_sale`.
- **Top Operators**: Ranking of operators by B2C volume.
- **Growth Metrics**: Monthly/Weekly B2C sales trends.

### UI Design:
- A new section in Admin Reports: `B2C Revenue Analysis`.
- Visual charts (Profit vs. Volume).

---

## 3. Implementation Steps
- [ ] Operator Dashboard: Implement specialized B2C sales query in `SalesReportController`.
- [ ] Operator UI: Create the `owner.report.b2c_sale` view.
- [ ] Admin Dashboard: Implement commission aggregation query in `ReportController`.
- [ ] Admin UI: Create the `admin.report.b2c_performance` view.
- [ ] Verification: Compare report sums with the `Transactions` ledger.
