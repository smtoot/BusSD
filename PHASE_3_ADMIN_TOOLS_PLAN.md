# 🔧 Phase 3: Admin Tools & Passenger Experience - Implementation Plan

**Date:** February 6, 2026
**Status:** Planning → Implementation
**Priority:** High (Completes B2C Ecosystem)

---

## 🎯 Objective

Complete the B2C ecosystem by implementing admin passenger management and ground staff tools that were missing from the original B2C implementation.

---

## 📋 Features to Implement

### Feature 1: **Admin Passenger Management Panel** ⭐ Priority 1
**Scope:** Full CRUD and analytics for app passengers

#### Sub-Features:
1. **Passenger List Page**
   - Searchable/filterable table
   - Columns: Name, Email, Mobile, Total Bookings, Total Spent, Status, Join Date
   - Pagination (15 per page)
   - Export to CSV

2. **Passenger Details Page**
   - Personal information
   - Booking history (all trips)
   - Total statistics (bookings, revenue, cancellations)
   - Account status management
   - Activity log

3. **Passenger Actions**
   - View full profile
   - View booking history
   - Ban/suspend account
   - Unban account
   - Send notification (future)

4. **Analytics Dashboard**
   - Total passengers count
   - Active vs inactive
   - Top passengers by spending
   - Growth trends
   - Registration chart

**Time Estimate:** 6-8 hours

---

### Feature 2: **Ground Staff QR Scanner** ⭐ Priority 2
**Scope:** Mobile-friendly ticket verification

#### Sub-Features:
1. **QR Scanner Interface**
   - Camera access for QR scanning
   - Manual ticket number entry fallback
   - Scan result display
   - Verification status

2. **Ticket Verification**
   - Validate ticket authenticity
   - Check ticket status (valid/used/cancelled)
   - Show passenger details
   - Show seat information
   - Trip details

3. **Mark as Boarded**
   - Update ticket status to "boarded"
   - Timestamp boarding time
   - Show boarding confirmation

4. **Passenger Manifest**
   - List all passengers for a trip
   - Filter by boarded/not boarded
   - Search passengers
   - Export manifest

**Time Estimate:** 8-10 hours

---

### Feature 3: **Seat Blocking System** ⭐ Priority 3
**Scope:** Prevent double-booking between B2C and counter

#### Sub-Features:
1. **Real-time Seat Availability**
   - Sync B2C bookings with counter system
   - Show blocked seats in counter view
   - Visual indication of B2C vs counter seats

2. **Temporary Seat Blocks**
   - Block seats during B2C checkout (pending payment)
   - Auto-release after timeout (15 minutes)
   - Manual release on payment failure

3. **Conflict Resolution**
   - Detect double-booking attempts
   - Prevent counter from booking B2C-held seats
   - Alert on conflicts

4. **Seat Status Types**
   - Available
   - B2C Booked (confirmed)
   - B2C Pending (payment pending)
   - Counter Booked
   - Blocked (maintenance/reserved)

**Time Estimate:** 10-12 hours

---

## 📊 Implementation Order

### **Day 1: Admin Passenger Management** (6-8 hours)

#### Task 1: Database & Models (1h)
- Already exists (Passenger model)
- Add any missing methods/relationships
- Add scopes for filtering

#### Task 2: Admin Routes & Controller (2h)
- Create `AdminPassengerController`
- Routes: index, show, ban, unban, export
- Implement search and filters

#### Task 3: Views - Passenger List (2h)
- Create `admin/passengers/index.blade.php`
- Searchable table
- Filters (status, date range)
- Export button

#### Task 4: Views - Passenger Details (2h)
- Create `admin/passengers/show.blade.php`
- Profile card
- Booking history table
- Statistics widgets
- Action buttons (ban/unban)

#### Task 5: Analytics (1h)
- Passenger statistics
- Growth charts
- Top spenders

---

### **Day 2: QR Scanner & Manifest** (8-10 hours)

#### Task 1: QR Code Generation (2h)
- Add QR code to booking confirmations
- Generate unique ticket codes
- Store QR data in bookings

#### Task 2: Scanner Interface (3h)
- Create scanner page for drivers/staff
- Camera integration (html5-qrcode library)
- Manual entry fallback
- Mobile-responsive UI

#### Task 3: Verification Logic (2h)
- Validate ticket authenticity
- Check ticket status
- Mark as boarded
- Prevent re-boarding

#### Task 4: Passenger Manifest (3h)
- Trip manifest page
- List all passengers for trip
- Boarding status indicators
- Export manifest PDF/CSV

---

### **Day 3: Seat Blocking** (10-12 hours)

#### Task 1: Database Changes (2h)
- Add `seat_status` table or column
- Track seat locks/blocks
- Add timeout mechanism

#### Task 2: B2C Checkout Integration (3h)
- Block seats on checkout start
- Release on payment success/failure
- Handle timeout

#### Task 3: Counter Integration (4h)
- Show B2C-blocked seats
- Prevent booking blocked seats
- Visual indicators

#### Task 4: Conflict Resolution (3h)
- Detect conflicts
- Auto-release expired blocks
- Admin override capability

---

## 🛠️ Technical Stack

### Backend
- **Controllers:** AdminPassengerController, QRScannerController
- **Models:** Passenger (existing), SeatBlock (new)
- **Jobs:** ReleaseSeatBlocks (scheduled)

### Frontend
- **QR Scanner:** html5-qrcode.js library
- **Charts:** ApexCharts (already in use)
- **UI:** Bootstrap 5 (already in use)

### Database
- **New Tables:**
  - `seat_blocks` (id, booking_id, seat_id, trip_id, expires_at, status)
- **Modified Tables:**
  - `booked_tickets` - add `boarded_at` timestamp

---

## 📁 Files to Create/Modify

### New Files
```
core/app/Http/Controllers/Admin/PassengerController.php
core/app/Http/Controllers/Owner/QRScannerController.php
core/app/Models/SeatBlock.php
core/app/Jobs/ReleaseSeatBlocks.php
core/resources/views/admin/passengers/index.blade.php
core/resources/views/admin/passengers/show.blade.php
core/resources/views/owner/scanner/index.blade.php
core/resources/views/owner/scanner/manifest.blade.php
core/database/migrations/2026_02_06_create_seat_blocks_table.php
```

### Modified Files
```
core/routes/admin.php - Add passenger routes
core/routes/owner.php - Add scanner routes
core/app/Models/Passenger.php - Add scopes and methods
core/app/Models/BookedTicket.php - Add boarding methods
core/resources/views/admin/partials/sidenav.json - Add menu
```

---

## 🚀 Let's Start with Feature 1: Admin Passenger Management

**Ready to proceed?** I'll start by implementing the admin passenger management panel.

### First Step: Admin Passenger List Page
- Create controller
- Create routes
- Create view with search/filters
- Implement export

Shall I begin? 🚀
