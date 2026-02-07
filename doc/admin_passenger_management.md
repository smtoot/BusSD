# Admin Module: Passenger Management Spec

## 1. Overview
As the platform expands to B2C, the Super Admin (Platform Owner) needs full visibility and control over the passenger database. This module will be added to the Admin Dashboard sidebar.

---

## 2. Admin Features
Following the existing system's user management pattern, the "Manage Passengers" module will include:

### 2.1 The Passenger List
- **All Passengers**: Searchable list by Name, Email, or Phone.
- **Status Filters**: Active, Banned, Email Unverified, Mobile Unverified.
- **Quick Actions**: "View Details" and "Login as Passenger" (optional for support).

### 2.2 Passenger Detail View
- **Profile Management**: Update name, email, and mobile.
- **Account Status**: Toggle Ban/Unban with a "Ban Reason" field.
- **Verification Controls**: Manually mark email/phone as verified.
- **Booking History**: Tab showing all `BookedTicket` records for this passenger.
- **Notification Log**: View all SMS/Email alerts sent to this passenger.

---

## 3. Technical Implementation
- **Controller**: `core/app/Http/Controllers/Admin/ManagePassengersController.php`
- **Routes**: Add to `core/routes/admin.php` within the `admin` middleware group.
- **Sidebar**: Add a new menu item "Manage Passengers" under the "Passenger Management" category.

### Sample API / Route Structure:
```php
Route::controller('ManagePassengersController')->name('passengers.')->prefix('passengers')->group(function(){
    Route::get('/', 'allPassengers')->name('all');
    Route::get('active', 'activePassengers')->name('active');
    Route::get('banned', 'bannedPassengers')->name('banned');
    Route::get('detail/{id}', 'detail')->name('detail');
    Route::post('update/{id}', 'update')->name('update');
    Route::post('status/{id}', 'status')->name('status'); // Ban/Unban
});
```

---

## 4. Acceptance Criteria
- [ ] Admin can see a total count of passengers on the Dashboard.
- [ ] Admin can successfully ban a passenger, preventing them from logging into the Flutter app.
- [ ] Admin can view a passenger's full booking history sorted by date.
- [ ] UI is consistent with the existing Admin template (colors, tables, buttons).

---

## 5. Sudan Context
- **Phone Lookup**: Ensure the search box handles Sudanese phone number formats (+249) correctly.
- **BOK Transactions**: Admin Detail view should show if a passenger has used Bank of Khartoum for their recent bookings.
