# Admin vs Owner Panel Access Control Fixes

## Summary

This document describes the changes made to fix access control issues in the TransLab transport ticket booking system. The system follows a **bus aggregator model** (similar to RedBus) where:

- **Admins** define global infrastructure (routes, fleet types, seat layouts)
- **Bus Companies (Owners/Operators)** manage their own operational resources (counters, schedules, vehicles, trips) and choose from admin-defined resources

## Changes Made

### 1. Admin/RouteController.php

**File:** `core/app/Http/Controllers/Admin/RouteController.php`

**Changes:** Added CRUD methods for route management

**New Methods Added:**
- `create()` - Display route creation form
- `store(Request $request)` - Create new route with `owner_id = 0` (global route)
- `edit($id)` - Display route edit form
- `update(Request $request, $id)` - Update existing route
- `destroy($id)` - Delete route

**Key Change:** Routes created by Admin have `owner_id = 0`, making them available to all owners.

---

### 2. Admin/FleetController.php

**File:** `core/app/Http/Controllers/Admin/FleetController.php`

**Changes:** Added CRUD methods for fleet type management

**New Methods Added:**
- `createFleetType()` - Display fleet type creation form
- `storeFleetType(Request $request)` - Create new fleet type with `owner_id = 0` (global fleet type)
- `editFleetType($id)` - Display fleet type edit form
- `updateFleetType(Request $request, $id)` - Update existing fleet type
- `destroyFleetType($id)` - Delete fleet type

**Key Change:** Fleet types created by Admin have `owner_id = 0`, making them available to all owners.

---

### 3. Owner/TripController.php

**File:** `core/app/Http/Controllers/Owner/TripController.php`

**Changes:** Removed route creation/editing methods

**Methods Removed:**
- `routeForm($id = 0)` - Removed route creation/editing form
- `routeStore(Request $request, $id = 0)` - Removed route creation/updating logic

**Method Updated:**
- `route()` - Now shows admin-defined routes (`owner_id = 0`) instead of owner's own routes

**Key Change:** Owners can now only VIEW and ACTIVATE/DEACTIVATE admin-defined routes.

---

### 4. Owner/FleetController.php

**File:** `core/app/Http/Controllers/Owner/FleetController.php`

**Changes:** Removed fleet type creation/editing methods and updated queries

**Methods Removed:**
- `fleetTypeStore(Request $request, $id = 0)` - Removed fleet type creation/updating logic

**Methods Updated:**
- `fleetType()` - Now shows admin-defined fleet types (`owner_id = 0`) instead of owner's own fleet types
- `vehicle()` - Now shows admin-defined fleet types (`owner_id = 0`) instead of owner's own fleet types

**Key Change:** Owners can now only VIEW and ACTIVATE/DEACTIVATE admin-defined fleet types.

---

### 5. Admin Routes

**File:** `core/routes/admin.php`

**Changes:** Added CRUD routes for route and fleet type management

**Routes Added:**
```php
// Route Manager
Route::controller('RouteController')->name('routes.')->prefix('routes')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('create', 'create')->name('create');
    Route::post('store', 'store')->name('store');
    Route::get('edit/{id}', 'edit')->name('edit');
    Route::post('update/{id}', 'update')->name('update');
    Route::post('delete/{id}', 'destroy')->name('delete');
    Route::get('show/{id}', 'show')->name('show');
});

// Fleet Manager
Route::controller('FleetController')->name('fleet.')->prefix('fleet')->group(function () {
    Route::get('vehicles', 'vehicles')->name('vehicles');
    Route::get('vehicles/show/{id}', 'vehicleShow')->name('vehicles.show');
    Route::get('fleet-types', 'fleetTypes')->name('fleet_types');
    Route::get('fleet-types/create', 'createFleetType')->name('fleet_types.create');
    Route::post('fleet-types/store', 'storeFleetType')->name('fleet_types.store');
    Route::get('fleet-types/edit/{id}', 'editFleetType')->name('fleet_types.edit');
    Route::post('fleet-types/update/{id}', 'updateFleetType')->name('fleet_types.update');
    Route::post('fleet-types/delete/{id}', 'destroyFleetType')->name('fleet_types.delete');
    Route::get('seat-layouts', 'seatLayouts')->name('seat_layouts');
    Route::post('seat-layouts/store/{id?}', 'seatLayoutStore')->name('seat_layouts.store');
    Route::post('seat-layouts/status/{id}', 'seatLayoutStatus')->name('seat_layouts.status');
    Route::get('export', 'export')->name('export');
});
```

---

### 6. Owner Routes

**File:** `core/routes/owner.php`

**Changes:** Removed CRUD routes for route and fleet type management

**Routes Removed:**
```php
//Trip Manage - Routes (Removed form and store routes)
Route::prefix('route')->name('route.')->group(function () {
    Route::get('', 'route')->name('index');
    Route::post('status/{id}', 'changeRouteStatus')->name('status');
});
```

**Routes Updated:**
```php
//Trip Manage - Stoppage (No change)

//Fleets Manage (Removed fleet type store route)
route::prefix('fleet-type')->name('fleet.type.')->group(function () {
    Route::get('', 'fleetType')->name('index');
    Route::post('status/{id}', 'fleetTypeStatus')->name('status');
});
```

---

### 7. Admin Views (Created)

**View Files Created:**
1. `core/resources/views/admin/routes/create.blade.php` - Route creation form
2. `core/resources/views/admin/routes/edit.blade.php` - Route edit form
3. `core/resources/views/admin/fleet/create_fleet_type.blade.php` - Fleet type creation form
4. `core/resources/views/admin/fleet/edit_fleet_type.blade.php` - Fleet type edit form

**Why Critical:** These views are required for Admins to create/edit routes and fleet types. Without them, Admins will encounter 404 errors.

---

### 8. Owner Views (Updated)

**View Files Updated:**
1. `core/resources/views/owner/route/index.blade.php` - Removed "Add New Route" button
2. `core/resources/views/owner/fleet_type/index.blade.php` - Removed "Add New Fleet Type" button
3. `core/resources/views/owner/vehicle/index.blade.php` - No changes needed (already correct)

**Why Important:** Owners now see selection/activation interface instead of CRUD interface for admin-defined resources.

---

## Resulting Access Control Model

### Admin Panel (Platform)
| Feature | Access | owner_id |
|----------|--------|-----------|
| Routes | CREATE/EDIT/DELETE ✅ | 0 (global) |
| Fleet Types | CREATE/EDIT/DELETE ✅ | 0 (global) |
| Counters | VIEW only ✅ | N/A |
| Schedules | VIEW only ✅ | N/A |
| Vehicles | VIEW only ✅ | N/A |
| Seat Layouts | CREATE/EDIT/DELETE ✅ | 0 (global) |

### Owner Panel (Bus Company)
| Feature | Access | owner_id |
|----------|--------|-----------|
| Routes | VIEW + ACTIVATE/DEACTIVATE ✅ | Shows owner_id=0 |
| Fleet Types | VIEW + ACTIVATE/DEACTIVATE ✅ | Shows owner_id=0 |
| Counters | CREATE/EDIT/DELETE ✅ | owner's own |
| Schedules | CREATE/EDIT/DELETE ✅ | owner's own |
| Vehicles | CREATE/EDIT/DELETE ✅ | owner's own |
| Seat Layouts | VIEW only ✅ | Shows owner_id=0 |

---

## Business Model (Bus Aggregator)

```
┌─────────────────────────────────────────────────────────────────┐
│                    ADMIN PANEL (Platform)                     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐            │
│  │  Routes  │  │Fleet     │  │Seat      │            │
│  │  CREATE  │  │Types      │  │Layouts    │            │
│  │  EDIT    │  │CREATE     │  │CREATE     │            │
│  │  DELETE  │  │EDIT       │  │EDIT       │            │
│  │  owner_id│  │DELETE     │  │DELETE     │            │
│  │    = 0  │  │owner_id  │  │owner_id  │            │
│  └──────────┘  │   = 0    │  │   = 0    │            │
│                 └──────────┘  └──────────┘            │
│  Available to ALL bus companies (owners)                │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│               OWNER PANEL (Bus Company)                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐         │
│  │  Routes  │  │Fleet     │  │Seat      │         │
│  │  VIEW    │  │Types      │  │Layouts    │         │
│  │ACTIVATE  │  │VIEW       │  │VIEW       │         │
│  │DEACTIVATE│  │ACTIVATE   │  │(only)    │         │
│  └──────────┘  └──────────┘  └──────────┘         │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐         │
│  │Counters │  │Schedules │  │Vehicles  │         │
│  │  CREATE  │  │  CREATE  │  │  CREATE  │         │
│  │  EDIT    │  │  EDIT    │  │  EDIT    │         │
│  │  DELETE  │  │  DELETE  │  │  DELETE  │         │
│  └──────────┘  └──────────┘  └──────────┘         │
│  Owner's own operational resources                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## Implementation Status

### ✅ Phase 1: COMPLETE

**Completed:**
- All backend controllers updated with proper CRUD methods
- All backend routes updated with correct endpoints
- Owner controller methods removed (route and fleet type CRUD)
- Owner controller queries updated to show admin-defined resources
- All admin view files created (routes and fleet types)
- Owner views updated to remove creation buttons

**Remaining Work:**
- Testing all CRUD operations to ensure they work correctly
- Creating additional admin views if needed

---

## Next Steps

### Immediate (Priority: Critical)
1. **Test Admin Route Creation** - Verify new routes appear in Owner panel with owner_id=0
2. **Test Admin Fleet Type Creation** - Verify new fleet types appear in Owner panel with owner_id=0
3. **Test Owner Route Activation** - Verify owners can activate/deactivate admin-defined routes
4. **Test Owner Fleet Type Activation** - Verify owners can activate/deactivate admin-defined fleet types
5. **Test Vehicle Creation** - Verify owners can select admin-defined fleet types when creating vehicles

### Future Enhancements (Priority: Important)
1. Add route filtering/searching for Owners (by starting/destination city)
2. Add route popularity metrics for Admin (which routes are most used)
3. Add route analytics (revenue, bookings per route)
4. Add fleet type usage statistics for Admin
5. Add fleet type recommendations based on booking patterns
6. Add counter location mapping (integrate with Google Maps)
7. Add schedule optimization suggestions based on historical data
8. Add schedule conflict detection (warn if overlapping schedules exist)
9. Add vehicle maintenance tracking
10. Add vehicle utilization reports
11. Add vehicle assignment history
12. Add trip profitability analysis
13. Add trip performance metrics
14. Add automated trip scheduling suggestions

### Documentation Updates (Priority: Medium)
1. Update user manual with new access control model
2. Create admin guide for defining routes and fleet types
3. Create owner guide for activating routes and selecting fleet types
4. Document API endpoints for new admin routes/fleet types

---

## Implementation Date

**Date:** 2026-02-08

**Status:** ✅ Phase 1 Complete - Backend and Views Ready for Testing

All access control fixes have been successfully implemented according to the bus aggregator business model. The system now correctly separates platform-level infrastructure from bus company operations.
