# ✅ B2C Features Testing Checklist

**Date:** February 6, 2026
**Purpose:** Verify all implemented B2C features work correctly before Phase 2

---

## 🎯 Testing Approach

**Test Account:** operator / operator
**Browser:** Chrome/Firefox (latest)
**Test Data:** Use existing demo data + create new test bookings if needed

---

## 1️⃣ Dashboard - B2C Performance Cards

### Test Cases:

**✅ Card 1: Counter Sales**
- [ ] Displays correct amount for current month
- [ ] Shows percentage change vs last month
- [ ] Arrow direction correct (↑ green for increase, ↓ red for decrease)
- [ ] "vs last month" label displays
- [ ] Cash register icon visible
- [ ] Click card (if linked) works

**✅ Card 2: B2C (App) Sales**
- [ ] Displays correct gross B2C sales amount
- [ ] Shows percentage change vs last month
- [ ] Trend arrow and color correct
- [ ] Mobile phone icon visible
- [ ] Amount matches B2C Sales report

**✅ Card 3: App Passengers**
- [ ] Shows correct count of B2C bookings this month
- [ ] "Bookings this month" label displays
- [ ] Users icon visible
- [ ] Count matches number of rows in B2C Sales report

**✅ Card 4: B2C Revenue Earned**
- [ ] Shows NET revenue (after commission deduction)
- [ ] Calculation correct: Gross × (1 - commission_rate/100)
- [ ] Shows percentage change vs last month
- [ ] Coins icon visible (green)
- [ ] Amount is LESS than B2C Sales (Card 2)

**✅ Sales Comparison Chart**
- [ ] Chart displays without errors
- [ ] Two series visible: "B2C (App)" and "Counter"
- [ ] Both lines display correctly
- [ ] Date range picker works
- [ ] Selecting different date ranges updates chart
- [ ] Chart title: "Sales Comparison (B2C vs Counter)"
- [ ] Currency symbol correct on hover

**✅ Mobile Responsiveness**
- [ ] Cards stack properly on mobile (< 768px width)
- [ ] All text readable
- [ ] No horizontal scroll
- [ ] Charts resize appropriately

---

## 2️⃣ B2C Notifications (Bell Icon)

### Test Cases:

**✅ Bell Icon**
- [ ] Bell icon visible in top-right navigation
- [ ] Badge counter displays if bookings in last 24h
- [ ] Badge shows correct count
- [ ] Badge hides if count = 0
- [ ] Bell icon clickable

**✅ Notification Dropdown**
- [ ] Dropdown opens on bell click
- [ ] "Recent App Bookings" header visible
- [ ] "Last 24 hours" label visible
- [ ] Dropdown scrollable if > 5 notifications

**✅ Notification Content**
- [ ] Shows loading spinner initially
- [ ] Displays passenger names correctly
- [ ] Shows seat count (e.g., "2 seat(s)")
- [ ] Trip title displays
- [ ] Amount shows with currency symbol
- [ ] Relative time displays (e.g., "5 minutes ago")
- [ ] Ticket icon visible (green)

**✅ Empty State**
- [ ] Shows inbox icon if no bookings
- [ ] "No new app bookings" message
- [ ] "Check back later" subtitle

**✅ Error State**
- [ ] If API fails, shows error icon and message
- [ ] "Failed to load notifications" text

**✅ Auto-Refresh**
- [ ] Notifications refresh every 60 seconds
- [ ] Badge updates automatically
- [ ] No console errors during refresh

**✅ Click Behavior**
- [ ] Clicking notification → navigates to B2C Sales page
- [ ] "View All B2C Sales" footer link works
- [ ] Dropdown closes after navigation

---

## 3️⃣ B2C Sales Report Page

### Test Cases:

**✅ Commission Info Banner**
- [ ] Blue info banner at top displays
- [ ] Shows commission rate (e.g., "10%")
- [ ] Indicates if custom or platform standard rate
- [ ] "Request Rate Review" button visible
- [ ] Button links to support ticket page

**✅ Performance Summary**
- [ ] "Total Gross Volume" calculates correctly
- [ ] "App Passengers" count matches Card 3
- [ ] "Estimated Net Revenue" shows after-commission amount
- [ ] All amounts use correct currency symbol

**✅ Export to CSV**
- [ ] "Export to CSV" button visible (green)
- [ ] Clicking button downloads CSV file
- [ ] Filename format: `b2c_sales_YYYY-MM-DD.csv`
- [ ] CSV contains all visible bookings
- [ ] CSV columns correct: Date, Passenger, Mobile, Trip, Amounts, Status
- [ ] CSV data matches table display
- [ ] Special characters (quotes) handled correctly

**✅ Sales Table**
- [ ] Journey dates display correctly
- [ ] Passenger names and mobile numbers show
- [ ] Trip titles display
- [ ] Gross amount correct (price × ticket_count)
- [ ] Commission shows with rate percentage
- [ ] Net credit calculates correctly
- [ ] Status badges display (Confirmed = green, Cancelled = yellow)

**✅ Pagination**
- [ ] Pagination displays if > 15 rows
- [ ] Page navigation works
- [ ] Correct number of items per page

**✅ Edge Cases**
- [ ] Empty state: "No B2C sales found" if no data
- [ ] Very long passenger names don't break layout
- [ ] Large amounts format correctly (commas, decimals)

---

## 4️⃣ Trip Feedbacks Page

### Test Cases:

**✅ Quick Stats Cards**
- [ ] "Average Rating" shows correct value (1 decimal)
- [ ] "Total Reviews" count accurate
- [ ] "5-Star Reviews" shows count and percentage
- [ ] "Low Ratings" shows ≤2★ count
- [ ] Low rating card red if > 0, green if 0
- [ ] Warning/check icon displays correctly

**✅ Feedback Table**
- [ ] Date column displays correctly
- [ ] Passenger names show
- [ ] Trip titles display
- [ ] Star ratings render (filled stars for rating, empty for remainder)
- [ ] Comments display or "N/A" if empty
- [ ] Pagination works

**✅ Empty State**
- [ ] "No feedback received yet" if no ratings
- [ ] Stats show "N/A" or 0 appropriately

---

## 5️⃣ Withdrawal Pages

### Test Cases:

**✅ Withdraw Money Page**
- [ ] Current balance displays at top
- [ ] Withdrawal methods show as cards
- [ ] Each method shows: name, limits, charges
- [ ] Clicking method opens withdrawal form
- [ ] Form validates correctly

**✅ Withdraw History**
- [ ] Shows all withdrawal requests
- [ ] Displays: TRX, method, amount, charges, status
- [ ] Status badges color-coded
- [ ] "New Withdrawal" button links correctly

---

## 6️⃣ Balance in Top Navigation

### Test Cases:

**✅ Balance Display**
- [ ] Balance button visible in top nav (desktop)
- [ ] Shows current balance with currency symbol
- [ ] "Balance: SDG XXX" format correct
- [ ] Clicking navigates to withdrawal page
- [ ] Hidden on mobile (< 576px) to save space
- [ ] Wallet icon displays

---

## 7️⃣ Cross-Feature Integration

### Test Cases:

**✅ Data Consistency**
- [ ] Dashboard B2C Sales = B2C Sales Report total
- [ ] Dashboard App Passengers = Count in B2C Sales
- [ ] Dashboard Revenue = B2C Sales × (1 - commission_rate/100)
- [ ] Balance in header = Balance on dashboard
- [ ] Notification count = B2C bookings in last 24h

**✅ Navigation Flow**
- [ ] Dashboard → B2C Sales (via sidebar menu)
- [ ] Bell notification → B2C Sales page
- [ ] Balance button → Withdraw page
- [ ] Commission banner → Support ticket page
- [ ] All sidebar menu items work

**✅ Date/Time Calculations**
- [ ] "This month" = current month data only
- [ ] "Last month" = previous calendar month
- [ ] "Last 24 hours" = exactly 24h ago
- [ ] Relative times update (e.g., "5 minutes ago" → "6 minutes ago")

---

## 8️⃣ Performance & Technical

### Test Cases:

**✅ Page Load Speed**
- [ ] Dashboard loads in < 3 seconds
- [ ] B2C Sales page loads in < 2 seconds
- [ ] No visible lag when clicking notifications
- [ ] Charts render smoothly

**✅ Browser Console**
- [ ] No JavaScript errors on dashboard
- [ ] No errors on B2C Sales page
- [ ] No errors during notification refresh
- [ ] No 404s or failed API requests

**✅ Database Queries**
- [ ] No N+1 query issues (check Laravel Debugbar if installed)
- [ ] Notifications use eager loading (passenger, trip)
- [ ] Dashboard queries optimized

**✅ Caching**
- [ ] View cache cleared
- [ ] Application cache cleared
- [ ] Browser cache doesn't show stale data

---

## 9️⃣ User Experience

### Test Cases:

**✅ Visual Design**
- [ ] Colors consistent (green = success, red = danger, etc.)
- [ ] Icons appropriate for each feature
- [ ] Cards have proper spacing
- [ ] Text readable (good contrast)
- [ ] Buttons clearly labeled

**✅ Usability**
- [ ] Can find B2C features without searching
- [ ] Notifications attention-grabbing but not annoying
- [ ] CSV export obvious and easy to use
- [ ] Commission info clear and transparent
- [ ] No confusing terminology

**✅ Accessibility**
- [ ] All buttons have labels
- [ ] Icons have tooltips where appropriate
- [ ] Color not the only indicator (icons + text)
- [ ] Keyboard navigation works

---

## 🐛 Known Issues to Watch For

### Common Problems:

1. **Commission Calculation Errors**
   - Watch for: Net revenue = 0 when it should have a value
   - Cause: Commission rate not found or wrong formula
   - Fix: Verify `$owner->b2c_commission ?? gs('b2c_commission')`

2. **Notification Badge Not Updating**
   - Watch for: Badge shows 0 when there are bookings
   - Cause: API returns wrong count or badge JS not updating
   - Fix: Check console, verify API response

3. **CSV Export Empty or Wrong Data**
   - Watch for: CSV downloads but has no rows
   - Cause: Blade @forelse loop issue or status filter wrong
   - Fix: Verify booking status = 1

4. **Dashboard Shows 0 When There's Data**
   - Watch for: All cards show 0
   - Cause: Date range issue or wrong table join
   - Fix: Check Carbon date filters

5. **"Undefined variable" Errors**
   - Watch for: Laravel error page
   - Cause: Variable not passed to view
   - Fix: Check controller compact() statement

---

## ✅ Final Verification

After testing all above, verify:

- [ ] No critical bugs found
- [ ] All calculations accurate
- [ ] UX feels smooth and intuitive
- [ ] Ready for real operator testing
- [ ] Documented any issues found

---

## 📝 Bug Report Template

If you find issues, document like this:

```
**Bug:** [Short description]
**Location:** [Page/feature where bug occurs]
**Steps to Reproduce:**
1. Go to...
2. Click...
3. See error...

**Expected:** [What should happen]
**Actual:** [What actually happens]
**Screenshot:** [If applicable]
**Priority:** Critical / High / Medium / Low
```

---

## 🔟 Phase 2: Enhanced Filters (NEW!)

### Test Cases:

**✅ Filter Panel Display**
- [ ] Filter panel displays above Performance Summary
- [ ] Date range input shows with calendar icon
- [ ] Trip dropdown shows all active trips
- [ ] Status dropdown shows All/Confirmed/Cancelled options
- [ ] Apply Filters button works
- [ ] Reset button visible

**✅ Date Range Picker**
- [ ] Clicking date input opens calendar
- [ ] Can select start date
- [ ] Can select end date
- [ ] Selected range displays in input (YYYY-MM-DD - YYYY-MM-DD)
- [ ] Calendar closes after selecting range
- [ ] Can clear selection
- [ ] Single date selection works

**✅ Trip Filter**
- [ ] Dropdown populated with operator's trips
- [ ] Trips sorted alphabetically
- [ ] "All Trips" option at top
- [ ] Selected trip shows correctly
- [ ] Filter applies on submit

**✅ Status Filter**
- [ ] "All Status" option available
- [ ] "Confirmed" option available
- [ ] "Cancelled" option available
- [ ] Default shows confirmed only (when no filter set)
- [ ] "All Status" shows both confirmed and cancelled

**✅ Filter Application**
- [ ] Clicking "Apply Filters" submits form
- [ ] URL updates with query parameters
- [ ] Table refreshes with filtered results
- [ ] Performance cards update to show filtered stats
- [ ] Pagination displays if needed

**✅ Active Filters Display**
- [ ] Shows below filter form when filters applied
- [ ] Date badge displays with calendar icon
- [ ] Trip badge displays with route icon and trip name
- [ ] Status badge displays with check icon
- [ ] Each badge has × to remove
- [ ] Clicking × removes that specific filter
- [ ] Removed filter refreshes page without that filter

**✅ Reset Button**
- [ ] Clicking "Reset" clears all filters
- [ ] Navigates to clean URL (no query params)
- [ ] Table shows default data (all confirmed B2C)
- [ ] Active filters badges disappear

**✅ Filter Combinations**
- [ ] Date + Trip filter works together
- [ ] Date + Status filter works together
- [ ] Trip + Status filter works together
- [ ] All three filters work together
- [ ] Correct data shown for all combinations
- [ ] AND logic applies (not OR)

**✅ Pagination with Filters**
- [ ] Pagination links include filter params
- [ ] Clicking page 2 preserves filters
- [ ] All pages show correct filtered data
- [ ] Pagination count reflects filtered results

**✅ Performance Summary with Filters**
- [ ] Total Gross Volume updates for filtered data
- [ ] App Passengers count matches filtered rows
- [ ] Estimated Net Revenue calculates correctly
- [ ] Stats accurate for all filter combinations

**✅ Enhanced CSV Export**
- [ ] Export button works with filters active
- [ ] CSV includes filter metadata as comments (# lines)
- [ ] CSV shows filtered results only
- [ ] Filename includes date range if filtered
- [ ] Filename includes trip ID if filtered
- [ ] Filename includes status if filtered
- [ ] Multiple filters reflected in filename
- [ ] Success notification shows record count
- [ ] Empty state prevents export with error message

**✅ CSV Export Examples**
- [ ] No filters: `b2c_sales_2026-02-06.csv`
- [ ] Date only: `b2c_sales_2026-01-01_to_2026-01-31.csv`
- [ ] Trip only: `b2c_sales_2026-02-06_trip5.csv`
- [ ] Status only: `b2c_sales_2026-02-06_confirmed.csv`
- [ ] All filters: `b2c_sales_2026-01-01_to_2026-01-31_trip5_confirmed.csv`

**✅ Edge Cases**
- [ ] Invalid date format handled gracefully
- [ ] Future dates allowed/disallowed as expected
- [ ] Empty date range (start = end) works
- [ ] Non-existent trip_id ignored or errors appropriately
- [ ] Status value outside 1/3 handled correctly
- [ ] No results shows "No B2C sales found" message
- [ ] Very long trip names don't break layout
- [ ] Special characters in dates handled correctly

**✅ Mobile Responsiveness**
- [ ] Filter form stacks properly on mobile
- [ ] Date picker usable on mobile
- [ ] Dropdowns work on mobile
- [ ] Buttons don't overlap
- [ ] Active filter badges wrap correctly
- [ ] No horizontal scroll

**✅ Browser Compatibility**
- [ ] Date picker works in Chrome
- [ ] Date picker works in Firefox
- [ ] Date picker works in Safari
- [ ] Filters work in all browsers
- [ ] CSV export works in all browsers

---

## 🎯 Next Steps After Testing

Once testing complete:
1. **Document any bugs found**
2. **Fix critical issues**
3. **Test Phase 2 filters thoroughly**
4. **Get operator feedback** if possible
5. **Consider Phase 3 enhancements** based on usage

---

**Good luck with testing! 🚀**
