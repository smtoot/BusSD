# Testing Guide - Priority 1 Implementation
## Boarding Points, Dropping Points, and Dynamic Pricing

This guide provides step-by-step instructions to test the Priority 1 features implementation.

---

## Table of Contents
1. [Database Verification](#1-database-verification)
2. [Model Testing with Tinker](#2-model-testing-with-tinker)
3. [Route Testing](#3-route-testing)
4. [Admin Panel Testing](#4-admin-panel-testing)
5. [Owner Panel Testing](#5-owner-panel-testing)
6. [Dynamic Pricing Logic Testing](#6-dynamic-pricing-logic-testing)
7. [Integration Testing](#7-integration-testing)

---

## 1. Database Verification

### 1.1 Check if tables exist
```bash
cd core
php artisan tinker
```

In Tinker:
```php
// Check if tables exist
Schema::hasTable('boarding_points');          // Should return true
Schema::hasTable('dropping_points');          // Should return true
Schema::hasTable('route_boarding_points');    // Should return true
Schema::hasTable('route_dropping_points');    // Should return true
Schema::hasTable('dynamic_pricing_rules');    // Should return true

// Check table structures
DB::select('DESCRIBE boarding_points');
DB::select('DESCRIBE dropping_points');
DB::select('DESCRIBE dynamic_pricing_rules');
```

### 1.2 Check migrations status
```bash
php artisan migrate:status
```

Look for these migrations:
- 2026_02_11_210000_create_boarding_points_table
- 2026_02_11_210001_create_route_boarding_points_table
- 2026_02_11_210002_create_dropping_points_table
- 2026_02_11_210003_create_route_dropping_points_table
- 2026_02_11_210004_create_dynamic_pricing_rules_table

---

## 2. Model Testing with Tinker

### 2.1 Test BoardingPoint Model

```bash
php artisan tinker
```

```php
// Create a new boarding point
$boardingPoint = new \App\Models\BoardingPoint();
$boardingPoint->owner_id = 0;  // 0 for global (admin), or specific owner ID
$boardingPoint->city_id = 1;   // Replace with valid city ID
$boardingPoint->counter_id = 1; // Replace with valid counter ID
$boardingPoint->name = 'Central Bus Station';
$boardingPoint->name_ar = 'محطة الحافلات المركزية';
$boardingPoint->address = '123 Main Street, City Center';
$boardingPoint->address_ar = '١٢٣ الشارع الرئيسي، وسط المدينة';
$boardingPoint->latitude = 24.7136;
$boardingPoint->longitude = 46.6753;
$boardingPoint->landmark = 'Near City Mall';
$boardingPoint->landmark_ar = 'بالقرب من مول المدينة';
$boardingPoint->phone = '+966123456789';
$boardingPoint->email = 'central@example.com';
$boardingPoint->type = 'bus_stand';
$boardingPoint->sort_order = 1;
$boardingPoint->status = 1;
$boardingPoint->save();

// Verify creation
\App\Models\BoardingPoint::find($boardingPoint->id);

// Test relationships
$boardingPoint->owner;
$boardingPoint->city;
$boardingPoint->counter;
$boardingPoint->routes;
```

### 2.2 Test DroppingPoint Model

```php
// Create a new dropping point
$droppingPoint = new \App\Models\DroppingPoint();
$droppingPoint->owner_id = 0;  // 0 for global (admin), or specific owner ID
$droppingPoint->city_id = 1;   // Replace with valid city ID
$droppingPoint->name = 'Airport Terminal 1';
$droppingPoint->name_ar = 'المطار المحطة 1';
$droppingPoint->address = 'King Fahd Road, Airport';
$droppingPoint->address_ar = 'طريق الملك فهد، المطار';
$droppingPoint->latitude = 24.9576;
$droppingPoint->longitude = 46.6988;
$droppingPoint->landmark = 'Near Terminal 1 Entrance';
$droppingPoint->landmark_ar = 'بالقرب من مدخل المحطة 1';
$droppingPoint->phone = '+966987654321';
$droppingPoint->email = 'airport@example.com';
$droppingPoint->type = 'airport';
$droppingPoint->sort_order = 1;
$droppingPoint->status = 1;
$droppingPoint->save();

// Verify creation
\App\Models\DroppingPoint::find($droppingPoint->id);

// Test relationships
$droppingPoint->owner;
$droppingPoint->city;
$droppingPoint->routes;
```

### 2.3 Test DynamicPricingRule Model

```php
// Create a surge pricing rule
$surgeRule = new \App\Models\DynamicPricingRule();
$surgeRule->owner_id = 0;  // 0 for global (admin), or specific owner ID
$surgeRule->name = 'Weekend Surge';
$surgeRule->name_ar = 'زيادة عطلة نهاية الأسبوع';
$surgeRule->rule_type = 'surge';
$surgeRule->operator_type = 'percentage';
$surgeRule->value = 20;  // 20% increase
$surgeRule->valid_from = now()->toDateString();
$surgeRule->valid_until = now()->addMonths(6)->toDateString();
$surgeRule->applicable_days = json_encode([5, 6]);  // Friday (5), Saturday (6)
$surgeRule->start_time = '18:00:00';
$surgeRule->end_time = '23:59:59';
$surgeRule->min_seats_available = 1;
$surgeRule->max_seats_available = 20;
$surgeRule->priority = 50;
$surgeRule->status = 1;
$surgeRule->save();

// Create an early bird rule
$earlyBirdRule = new \App\Models\DynamicPricingRule();
$earlyBirdRule->owner_id = 0;
$earlyBirdRule->name = 'Early Bird Discount';
$earlyBirdRule->name_ar = 'خصم الحجز المبكر';
$earlyBirdRule->rule_type = 'early_bird';
$earlyBirdRule->operator_type = 'percentage';
$earlyBirdRule->value = -10;  // 10% discount (negative)
$earlyBirdRule->valid_from = now()->toDateString();
$earlyBirdRule->valid_until = now()->addMonths(3)->toDateString();
$earlyBirdRule->applicable_days = json_encode([0, 1, 2, 3, 4, 5, 6]);  // All days
$earlyBirdRule->min_hours_before_departure = 72;  // Must book 72+ hours before
$earlyBirdRule->priority = 30;
$earlyBirdRule->status = 1;
$earlyBirdRule->save();

// Verify creation
\App\Models\DynamicPricingRule::find($surgeRule->id);
\App\Models\DynamicPricingRule::find($earlyBirdRule->id);

// Test relationships
$surgeRule->owner;
$surgeRule->routes;
$surgeRule->fleetTypes;
```

### 2.4 Test Route Assignment

```php
// Get a route (replace with valid route ID)
$route = \App\Models\Route::find(1);

// Assign boarding point to route
$route->boardingPoints()->attach($boardingPoint->id, [
    'pickup_time_offset' => 0,  // Minutes from route start
    'sort_order' => 1
]);

// Assign dropping point to route
$route->droppingPoints()->attach($droppingPoint->id, [
    'dropoff_time_offset' => 120,  // 120 minutes from route start
    'sort_order' => 1
]);

// Assign dynamic pricing rule to route
$route->dynamicPricingRules()->attach($surgeRule->id);

// Verify assignments
$route->boardingPoints;
$route->droppingPoints;
$route->dynamicPricingRules;
```

### 2.5 Test Trip Dynamic Pricing

```php
// Get a trip (replace with valid trip ID)
$trip = \App\Models\Trip::find(1);

// Test dynamic price calculation
$basePrice = 100;  // Base ticket price
$dynamicPrice = $trip->calculateDynamicPrice($basePrice);

echo "Base Price: $basePrice\n";
echo "Dynamic Price: $dynamicPrice\n";
echo "Price Change: " . ($dynamicPrice - $basePrice) . "\n";

// Test with different scenarios
$trip->departure_time = now()->addHours(80);  // 80+ hours before (early bird eligible)
$earlyBirdPrice = $trip->calculateDynamicPrice($basePrice);
echo "Early Bird Price: $earlyBirdPrice\n";

$trip->departure_time = now()->addHours(2);  // 2 hours before (last minute eligible)
$lastMinutePrice = $trip->calculateDynamicPrice($basePrice);
echo "Last Minute Price: $lastMinutePrice\n";
```

---

## 3. Route Testing

### 3.1 List Admin Routes

```bash
php artisan route:list --path=admin
```

Look for these routes:
- `GET/HEAD admin/boarding-points` - boarding-points.index
- `GET/HEAD admin/boarding-points/create` - boarding-points.create
- `POST admin/boarding-points` - boarding-points.store
- `GET/HEAD admin/boarding-points/{id}` - boarding-points.show
- `GET/HEAD admin/boarding-points/{id}/edit` - boarding-points.edit
- `PUT/PATCH admin/boarding-points/{id}` - boarding-points.update
- `DELETE admin/boarding-points/{id}` - boarding-points.destroy
- `GET/HEAD admin/boarding-points/{id}/assign` - boarding-points.assign
- `POST admin/boarding-points/{id}/assign` - boarding-points.assign.store

Similar routes for dropping-points and dynamic-pricing.

### 3.2 Test Routes with Curl

```bash
# Login to get admin token (adjust based on your auth system)
curl -X POST http://localhost/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# List boarding points (replace TOKEN with actual token)
curl -X GET http://localhost/admin/boarding-points \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"

# Create boarding point
curl -X POST http://localhost/admin/boarding-points \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "owner_id": 0,
    "city_id": 1,
    "counter_id": 1,
    "name": "Test Boarding Point",
    "name_ar": "نقطة صعود تجريبية",
    "address": "Test Address",
    "address_ar": "عنوان تجريبي",
    "latitude": 24.7136,
    "longitude": 46.6753,
    "landmark": "Test Landmark",
    "landmark_ar": "معلم تجريبي",
    "phone": "+966123456789",
    "email": "test@example.com",
    "type": "bus_stand",
    "sort_order": 1,
    "status": 1
  }'

# Show boarding point
curl -X GET http://localhost/admin/boarding-points/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"

# Update boarding point
curl -X PUT http://localhost/admin/boarding-points/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Updated Boarding Point",
    "name_ar": "نقطة صعود محدثة",
    "status": 1
  }'

# Delete boarding point
curl -X DELETE http://localhost/admin/boarding-points/1 \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"
```

---

## 4. Admin Panel Testing

### 4.1 Access Admin Panel

1. Open browser and navigate to: `http://localhost/admin`
2. Login with admin credentials
3. Navigate to sidebar menu

### 4.2 Test Boarding Points

**Create Boarding Point:**
1. Go to **Manage Routes** → **Boarding Points**
2. Click **Add New Boarding Point**
3. Fill in the form:
   - Owner: Select "All Owners" (for global) or specific owner
   - City: Select a city
   - Counter: Select a counter (optional)
   - Name (English): "Central Bus Station"
   - Name (Arabic): "محطة الحافلات المركزية"
   - Address (English): "123 Main Street"
   - Address (Arabic): "١٢٣ الشارع الرئيسي"
   - Latitude: 24.7136
   - Longitude: 46.6753
   - Landmark: "Near City Mall"
   - Phone: "+966123456789"
   - Email: "central@example.com"
   - Type: "Bus Stand"
   - Sort Order: 1
   - Status: Active
4. Click **Save**

**Verify:**
- Boarding point appears in the list
- All fields are displayed correctly
- Status badge shows "Active"

**Edit Boarding Point:**
1. Click **Edit** on the boarding point
2. Modify fields (e.g., change name)
3. Click **Save**

**Verify:**
- Changes are reflected in the list

**Assign to Route:**
1. Click **Assign to Routes** on a boarding point
2. Select route(s) from the list
3. Set pickup time offset (in minutes)
4. Click **Save**

**Verify:**
- Boarding point is assigned to selected routes
- Pickup time offset is saved

### 4.3 Test Dropping Points

**Create Dropping Point:**
1. Go to **Manage Routes** → **Dropping Points**
2. Click **Add New Dropping Point**
3. Fill in the form (similar to boarding point)
4. Click **Save**

**Verify:**
- Dropping point appears in the list

**Assign to Route:**
1. Click **Assign to Routes** on a dropping point
2. Select route(s) from the list
3. Set drop-off time offset (in minutes)
4. Click **Save**

### 4.4 Test Dynamic Pricing

**Create Surge Pricing Rule:**
1. Go to **Dynamic Pricing** → **Pricing Rules**
2. Click **Add New Pricing Rule**
3. Fill in the form:
   - Owner: "All Owners"
   - Name: "Weekend Surge"
   - Name (Arabic): "زيادة عطلة نهاية الأسبوع"
   - Rule Type: "Surge Pricing"
   - Operator Type: "Percentage"
   - Value: 20
   - Valid From: Today's date
   - Valid Until: 6 months from now
   - Applicable Days: Friday, Saturday
   - Start Time: 18:00
   - End Time: 23:59
   - Min Seats Available: 1
   - Max Seats Available: 20
   - Priority: 50
   - Status: Active
4. Click **Save**

**Create Early Bird Discount:**
1. Click **Add New Pricing Rule**
2. Fill in the form:
   - Name: "Early Bird Discount"
   - Name (Arabic): "خصم الحجز المبكر"
   - Rule Type: "Early Bird"
   - Operator Type: "Percentage"
   - Value: -10 (negative for discount)
   - Valid From: Today's date
   - Valid Until: 3 months from now
   - Applicable Days: All days
   - Min Hours Before Departure: 72
   - Priority: 30
   - Status: Active
3. Click **Save**

**Create Last Minute Surge:**
1. Click **Add New Pricing Rule**
2. Fill in the form:
   - Name: "Last Minute Surge"
   - Name (Arabic): "زيادة اللحظة الأخيرة"
   - Rule Type: "Last Minute"
   - Operator Type: "Percentage"
   - Value: 15
   - Valid From: Today's date
   - Valid Until: 6 months from now
   - Max Hours Before Departure: 6
   - Priority: 60
   - Status: Active
3. Click **Save**

**Verify:**
- All rules appear in the list
- Rule types are displayed correctly
- Values show positive for surges, negative for discounts

---

## 5. Owner Panel Testing

### 5.1 Access Owner Panel

1. Open browser and navigate to: `http://localhost/owner`
2. Login with owner credentials
3. Navigate to sidebar menu

### 5.2 Test Owner Boarding Points

**Create Owner Boarding Point:**
1. Go to **Manage Routes** → **Boarding Points**
2. Click **Add New Boarding Point**
3. Fill in the form (owner_id will be auto-set to current owner)
4. Click **Save**

**Verify:**
- Boarding point is created with owner_id = current owner
- Only owner's boarding points are visible

**Assign to Owner's Routes:**
1. Click **Assign to Routes** on a boarding point
2. Verify only owner's routes are listed
3. Select route(s) and set pickup time offset
4. Click **Save**

**Verify:**
- Boarding point is assigned to owner's routes only

### 5.3 Test Owner Dropping Points

Follow similar steps as boarding points.

### 5.4 Test Owner Dynamic Pricing

**Create Owner Pricing Rule:**
1. Go to **Dynamic Pricing** → **Pricing Rules**
2. Click **Add New Pricing Rule**
3. Fill in the form (owner_id will be auto-set to current owner)
4. Click **Save**

**Verify:**
- Pricing rule is created with owner_id = current owner
- Only owner's pricing rules are visible

---

## 6. Dynamic Pricing Logic Testing

### 6.1 Test Surge Pricing

```php
php artisan tinker
```

```php
// Setup
$trip = \App\Models\Trip::find(1);
$basePrice = 100;

// Set trip to weekend evening (Friday 8 PM)
$trip->departure_time = \Carbon\Carbon::now()->next(Carbon::FRIDAY)->setHour(20);
$trip->available_seats = 10;

// Calculate price
$price = $trip->calculateDynamicPrice($basePrice);
echo "Weekend Evening Price: $price (Expected: 120 with 20% surge)\n";
```

### 6.2 Test Early Bird Discount

```php
// Set trip to 80+ hours in the future
$trip->departure_time = \Carbon\Carbon::now()->addHours(80);
$trip->available_seats = 15;

// Calculate price
$price = $trip->calculateDynamicPrice($basePrice);
echo "Early Bird Price: $price (Expected: 90 with 10% discount)\n";
```

### 6.3 Test Last Minute Surge

```php
// Set trip to 2 hours in the future
$trip->departure_time = \Carbon\Carbon::now()->addHours(2);
$trip->available_seats = 5;

// Calculate price
$price = $trip->calculateDynamicPrice($basePrice);
echo "Last Minute Price: $price (Expected: 115 with 15% surge)\n";
```

### 6.4 Test Priority System

```php
// Create multiple rules with different priorities
$highPriority = new \App\Models\DynamicPricingRule();
$highPriority->name = 'High Priority Rule';
$highPriority->rule_type = 'surge';
$highPriority->operator_type = 'percentage';
$highPriority->value = 30;
$highPriority->priority = 100;
$highPriority->status = 1;
$highPriority->save();

$lowPriority = new \App\Models\DynamicPricingRule();
$lowPriority->name = 'Low Priority Rule';
$lowPriority->rule_type = 'surge';
$lowPriority->operator_type = 'percentage';
$lowPriority->value = 10;
$lowPriority->priority = 10;
$lowPriority->status = 1;
$lowPriority->save();

// Assign both to same route
$route = \App\Models\Route::find(1);
$route->dynamicPricingRules()->attach([$highPriority->id, $lowPriority->id]);

// Test that high priority rule takes precedence
$trip->departure_time = \Carbon\Carbon::now()->addHours(5);
$price = $trip->calculateDynamicPrice($basePrice);
echo "Priority Test Price: $price (Expected: 130 with 30% from high priority)\n";
```

### 6.5 Test Fixed Amount vs Percentage

```php
// Create fixed amount rule
$fixedRule = new \App\Models\DynamicPricingRule();
$fixedRule->name = 'Fixed Amount Rule';
$fixedRule->rule_type = 'custom';
$fixedRule->operator_type = 'fixed';
$fixedRule->value = 25;  // Fixed $25 increase
$fixedRule->priority = 50;
$fixedRule->status = 1;
$fixedRule->save();

$trip->departure_time = \Carbon\Carbon::now()->addHours(5);
$price = $trip->calculateDynamicPrice($basePrice);
echo "Fixed Amount Price: $price (Expected: 125 with $25 fixed increase)\n";
```

---

## 7. Integration Testing

### 7.1 Test Complete Booking Flow with Boarding/Dropping Points

1. **Setup:**
   - Create a route with boarding and dropping points
   - Create trips for the route
   - Apply dynamic pricing rules

2. **User Flow:**
   - User searches for routes
   - User selects a route
   - User sees available boarding and dropping points with times
   - User selects boarding and dropping points
   - User sees dynamic price (base + surge/discount)
   - User completes booking

3. **Verify:**
   - Boarding point pickup time is calculated correctly
   - Dropping point drop-off time is calculated correctly
   - Dynamic price is applied correctly
   - Booking includes selected boarding and dropping points

### 7.2 Test Multi-Owner Scenario

1. **Setup:**
   - Create global boarding points (owner_id = 0)
   - Create owner-specific boarding points
   - Create routes for different owners

2. **Admin View:**
   - Admin sees all boarding points (global + owner-specific)
   - Admin can assign global points to any route

3. **Owner View:**
   - Owner sees only their boarding points + global points
   - Owner can assign their points to their routes only
   - Owner cannot assign global points (read-only)

### 7.3 Test Route Assignment Time Offsets

```php
php artisan tinker
```

```php
// Create a route
$route = \App\Models\Route::create([
    'owner_id' => 1,
    'from_city_id' => 1,
    'to_city_id' => 2,
    'distance' => 300,
    'duration' => 300,  // 5 hours in minutes
    'status' => 1
]);

// Create boarding points
$bp1 = \App\Models\BoardingPoint::create([
    'owner_id' => 0,
    'city_id' => 1,
    'name' => 'Point A',
    'latitude' => 24.7136,
    'longitude' => 46.6753,
    'status' => 1
]);

$bp2 = \App\Models\BoardingPoint::create([
    'owner_id' => 0,
    'city_id' => 1,
    'name' => 'Point B',
    'latitude' => 24.7236,
    'longitude' => 46.6853,
    'status' => 1
]);

// Assign with different time offsets
$route->boardingPoints()->attach($bp1->id, [
    'pickup_time_offset' => 0,  // First pickup
    'sort_order' => 1
]);

$route->boardingPoints()->attach($bp2->id, [
    'pickup_time_offset' => 15,  // 15 minutes after first pickup
    'sort_order' => 2
]);

// Create a trip with departure time
$trip = \App\Models\Trip::create([
    'route_id' => $route->id,
    'schedule_id' => 1,
    'departure_time' => '2026-02-12 08:00:00',
    'status' => 1
]);

// Calculate pickup times
$boardingPoints = $trip->route->boardingPoints()->orderBy('pivot_sort_order')->get();
foreach ($boardingPoints as $bp) {
    $pickupTime = $trip->departure_time->addMinutes($bp->pivot->pickup_time_offset);
    echo "{$bp->name}: {$pickupTime->format('H:i')}\n";
}

// Expected output:
// Point A: 08:00
// Point B: 08:15
```

---

## 8. Common Issues and Solutions

### Issue 1: Migration fails with "Table already exists"

**Solution:**
```bash
# Check migration status
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback --step=5

# Or force migrate (use with caution)
php artisan migrate:fresh
```

### Issue 2: Boarding point not showing in owner panel

**Solution:**
- Check owner_id is set correctly
- Global points (owner_id = 0) should be visible to all owners
- Owner-specific points should only be visible to that owner

### Issue 3: Dynamic pricing not applying

**Solution:**
- Check rule status is Active (1)
- Check valid_from and valid_until dates
- Check applicable_days and time ranges
- Check min/max seats available
- Check trip departure_time matches rule conditions
- Check rule priority

### Issue 4: Route assignment not working

**Solution:**
- Check route exists
- Check boarding/dropping point exists
- Check both belong to same city or compatible cities
- Check owner permissions

---

## 9. Performance Testing

### 9.1 Test with Large Dataset

```php
php artisan tinker
```

```php
// Create 1000 boarding points
for ($i = 1; $i <= 1000; $i++) {
    \App\Models\BoardingPoint::create([
        'owner_id' => 0,
        'city_id' => 1,
        'name' => "Boarding Point $i",
        'latitude' => 24.7136 + ($i * 0.0001),
        'longitude' => 46.6753 + ($i * 0.0001),
        'status' => 1
    ]);
}

// Test query performance
$start = microtime(true);
$points = \App\Models\BoardingPoint::where('status', 1)->get();
$end = microtime(true);
echo "Query time: " . ($end - $start) . " seconds\n";
echo "Records: " . $points->count() . "\n";
```

---

## 10. Security Testing

### 10.1 Test Owner Isolation

```php
// Owner 1 should not see Owner 2's boarding points
$owner1 = \App\Models\Owner::find(1);
$owner2 = \App\Models\Owner::find(2);

$owner1Points = \App\Models\BoardingPoint::where('owner_id', $owner1->id)->get();
$owner2Points = \App\Models\BoardingPoint::where('owner_id', $owner2->id)->get();

echo "Owner 1 points: " . $owner1Points->count() . "\n";
echo "Owner 2 points: " . $owner2Points->count() . "\n";

// Verify no overlap
$owner1Ids = $owner1Points->pluck('id')->toArray();
$owner2Ids = $owner2Points->pluck('id')->toArray();
$overlap = array_intersect($owner1Ids, $owner2Ids);
echo "Overlap: " . count($overlap) . " (Expected: 0)\n";
```

### 10.2 Test Admin Access Control

```php
// Admin should see all points
$adminPoints = \App\Models\BoardingPoint::all();
echo "Total points (admin view): " . $adminPoints->count() . "\n";

// Owner should see only their points + global points
$ownerPoints = \App\Models\BoardingPoint::where(function($query) {
    $query->where('owner_id', 0)
          ->orWhere('owner_id', auth()->id());
})->get();
echo "Owner visible points: " . $ownerPoints->count() . "\n";
```

---

## 11. API Testing (Optional)

If you have API endpoints set up:

```bash
# Test API authentication
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Test API endpoints
curl -X GET http://localhost/api/boarding-points \
  -H "Authorization: Bearer TOKEN" \
  -H "Accept: application/json"

curl -X POST http://localhost/api/boarding-points \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "API Test Point",
    "latitude": 24.7136,
    "longitude": 46.6753,
    "status": 1
  }'
```

---

## 12. Final Checklist

Before considering testing complete:

- [ ] All 5 database tables are created successfully
- [ ] Boarding points can be created, read, updated, and deleted
- [ ] Dropping points can be created, read, updated, and deleted
- [ ] Dynamic pricing rules can be created, read, updated, and deleted
- [ ] Boarding points can be assigned to routes
- [ ] Dropping points can be assigned to routes
- [ ] Dynamic pricing rules can be assigned to routes
- [ ] Admin can see and manage all resources
- [ ] Owners can see and manage only their resources
- [ ] Global resources (owner_id = 0) are visible to all owners
- [ ] Dynamic pricing applies correctly based on rules
- [ ] Priority system works correctly
- [ ] Time offsets for boarding/dropping points work correctly
- [ ] GPS coordinates are stored and retrieved correctly
- [ ] Multi-language support (English/Arabic) works
- [ ] Status management (Active/Inactive) works
- [ ] Sort order is respected

---

## Conclusion

This testing guide covers all aspects of the Priority 1 implementation. Follow each section systematically to ensure the implementation is working correctly and aligned with the redBus business model.

For any issues encountered during testing, refer to the "Common Issues and Solutions" section or check the implementation plan document: `plans/priority-1-boarding-dropping-points-dynamic-pricing.md`
