#!/bin/bash

# Quick Test Script for Priority 1 Implementation
# Boarding Points, Dropping Points, and Dynamic Pricing

echo "========================================="
echo "Priority 1 Implementation - Quick Test"
echo "========================================="
echo ""

cd core

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
PASSED=0
FAILED=0

# Function to print test result
print_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASSED${NC}: $2"
        ((PASSED++))
    else
        echo -e "${RED}✗ FAILED${NC}: $2"
        ((FAILED++))
    fi
}

echo "1. Checking Database Tables..."
echo "--------------------------------"

# Check if tables exist
TABLES=("boarding_points" "dropping_points" "route_boarding_points" "route_dropping_points" "dynamic_pricing_rules")

for table in "${TABLES[@]}"; do
    php artisan tinker --execute="echo Schema::hasTable('$table') ? '1' : '0';" 2>/dev/null | grep -q "1"
    print_result $? "Table '$table' exists"
done

echo ""
echo "2. Checking Migrations Status..."
echo "--------------------------------"

# Check migrations
php artisan migrate:status | grep -q "2026_02_11_210000_create_boarding_points_table"
print_result $? "Migration: boarding_points table"

php artisan migrate:status | grep -q "2026_02_11_210001_create_route_boarding_points_table"
print_result $? "Migration: route_boarding_points table"

php artisan migrate:status | grep -q "2026_02_11_210002_create_dropping_points_table"
print_result $? "Migration: dropping_points table"

php artisan migrate:status | grep -q "2026_02_11_210003_create_route_dropping_points_table"
print_result $? "Migration: route_dropping_points table"

php artisan migrate:status | grep -q "2026_02_11_210004_create_dynamic_pricing_rules_table"
print_result $? "Migration: dynamic_pricing_rules table"

echo ""
echo "3. Checking Model Classes..."
echo "--------------------------------"

# Check if model files exist
MODELS=("app/Models/BoardingPoint.php" "app/Models/DroppingPoint.php" "app/Models/DynamicPricingRule.php")

for model in "${MODELS[@]}"; do
    if [ -f "$model" ]; then
        print_result 0 "Model file exists: $model"
    else
        print_result 1 "Model file exists: $model"
    fi
done

echo ""
echo "4. Checking Controller Classes..."
echo "--------------------------------"

# Check if controller files exist
CONTROLLERS=(
    "app/Http/Controllers/Admin/BoardingPointController.php"
    "app/Http/Controllers/Admin/DroppingPointController.php"
    "app/Http/Controllers/Admin/DynamicPricingController.php"
    "app/Http/Controllers/Owner/BoardingPointController.php"
    "app/Http/Controllers/Owner/DroppingPointController.php"
    "app/Http/Controllers/Owner/DynamicPricingController.php"
)

for controller in "${CONTROLLERS[@]}"; do
    if [ -f "$controller" ]; then
        print_result 0 "Controller file exists: $controller"
    else
        print_result 1 "Controller file exists: $controller"
    fi
done

echo ""
echo "5. Checking Routes..."
echo "--------------------------------"

# Check if routes are registered
php artisan route:list --path=admin/boarding-points 2>/dev/null | grep -q "boarding-points.index"
print_result $? "Route: admin/boarding-points.index"

php artisan route:list --path=admin/dropping-points 2>/dev/null | grep -q "dropping-points.index"
print_result $? "Route: admin/dropping-points.index"

php artisan route:list --path=admin/dynamic-pricing 2>/dev/null | grep -q "dynamic-pricing.index"
print_result $? "Route: admin/dynamic-pricing.index"

php artisan route:list --path=owner/boarding-points 2>/dev/null | grep -q "boarding-points.index"
print_result $? "Route: owner/boarding-points.index"

php artisan route:list --path=owner/dropping-points 2>/dev/null | grep -q "dropping-points.index"
print_result $? "Route: owner/dropping-points.index"

php artisan route:list --path=owner/dynamic-pricing 2>/dev/null | grep -q "dynamic-pricing.index"
print_result $? "Route: owner/dynamic-pricing.index"

echo ""
echo "6. Checking Sidenav Configuration..."
echo "--------------------------------"

# Check if sidenav.json has been updated
if grep -q "boarding-points" resources/views/admin/partials/sidenav.json; then
    print_result 0 "Sidenav: Boarding Points menu item exists"
else
    print_result 1 "Sidenav: Boarding Points menu item exists"
fi

if grep -q "dropping-points" resources/views/admin/partials/sidenav.json; then
    print_result 0 "Sidenav: Dropping Points menu item exists"
else
    print_result 1 "Sidenav: Dropping Points menu item exists"
fi

if grep -q "dynamic-pricing" resources/views/admin/partials/sidenav.json; then
    print_result 0 "Sidenav: Dynamic Pricing menu item exists"
else
    print_result 1 "Sidenav: Dynamic Pricing menu item exists"
fi

echo ""
echo "7. Testing Model Relationships..."
echo "--------------------------------"

# Test BoardingPoint model
php artisan tinker --execute="
\$bp = new App\Models\BoardingPoint();
echo method_exists(\$bp, 'owner') ? '1' : '0';
echo ',';
echo method_exists(\$bp, 'city') ? '1' : '0';
echo ',';
echo method_exists(\$bp, 'counter') ? '1' : '0';
echo ',';
echo method_exists(\$bp, 'routes') ? '1' : '0';
" 2>/dev/null | grep -q "1,1,1,1"
print_result $? "BoardingPoint model has all relationships"

# Test DroppingPoint model
php artisan tinker --execute="
\$dp = new App\Models\DroppingPoint();
echo method_exists(\$dp, 'owner') ? '1' : '0';
echo ',';
echo method_exists(\$dp, 'city') ? '1' : '0';
echo ',';
echo method_exists(\$dp, 'routes') ? '1' : '0';
" 2>/dev/null | grep -q "1,1,1"
print_result $? "DroppingPoint model has all relationships"

# Test DynamicPricingRule model
php artisan tinker --execute="
\$dpr = new App\Models\DynamicPricingRule();
echo method_exists(\$dpr, 'owner') ? '1' : '0';
echo ',';
echo method_exists(\$dpr, 'routes') ? '1' : '0';
echo ',';
echo method_exists(\$dpr, 'fleetTypes') ? '1' : '0';
" 2>/dev/null | grep -q "1,1,1"
print_result $? "DynamicPricingRule model has all relationships"

# Test Route model
php artisan tinker --execute="
\$route = new App\Models\Route();
echo method_exists(\$route, 'boardingPoints') ? '1' : '0';
echo ',';
echo method_exists(\$route, 'droppingPoints') ? '1' : '0';
echo ',';
echo method_exists(\$route, 'dynamicPricingRules') ? '1' : '0';
" 2>/dev/null | grep -q "1,1,1"
print_result $? "Route model has boarding/dropping/pricing relationships"

# Test Trip model
php artisan tinker --execute="
\$trip = new App\Models\Trip();
echo method_exists(\$trip, 'calculateDynamicPrice') ? '1' : '0';
" 2>/dev/null | grep -q "1"
print_result $? "Trip model has calculateDynamicPrice method"

echo ""
echo "8. Testing Database Schema..."
echo "--------------------------------"

# Check boarding_points table structure
php artisan tinker --execute="
\$columns = Schema::getColumnListing('boarding_points');
echo in_array('owner_id', \$columns) ? '1' : '0';
echo ',';
echo in_array('city_id', \$columns) ? '1' : '0';
echo ',';
echo in_array('latitude', \$columns) ? '1' : '0';
echo ',';
echo in_array('longitude', \$columns) ? '1' : '0';
echo ',';
echo in_array('type', \$columns) ? '1' : '0';
" 2>/dev/null | grep -q "1,1,1,1,1"
print_result $? "boarding_points table has required columns"

# Check dynamic_pricing_rules table structure
php artisan tinker --execute="
\$columns = Schema::getColumnListing('dynamic_pricing_rules');
echo in_array('rule_type', \$columns) ? '1' : '0';
echo ',';
echo in_array('operator_type', \$columns) ? '1' : '0';
echo ',';
echo in_array('value', \$columns) ? '1' : '0';
echo ',';
echo in_array('priority', \$columns) ? '1' : '0';
echo ',';
echo in_array('valid_from', \$columns) ? '1' : '0';
echo ',';
echo in_array('valid_until', \$columns) ? '1' : '0';
" 2>/dev/null | grep -q "1,1,1,1,1,1"
print_result $? "dynamic_pricing_rules table has required columns"

echo ""
echo "========================================="
echo "Test Summary"
echo "========================================="
echo -e "${GREEN}Passed: $PASSED${NC}"
echo -e "${RED}Failed: $FAILED${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed! ✓${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Run: php artisan tinker"
    echo "2. Create test data using the commands in TESTING_GUIDE.md"
    echo "3. Access admin panel at http://localhost/admin"
    echo "4. Test the UI for boarding points, dropping points, and dynamic pricing"
    exit 0
else
    echo -e "${RED}Some tests failed. Please review the output above.${NC}"
    echo ""
    echo "Common issues:"
    echo "- Run: php artisan migrate:status (to check migrations)"
    echo "- Run: php artisan migrate:fresh (to reset and re-run migrations)"
    echo "- Check: core/routes/admin.php and core/routes/owner.php (for routes)"
    exit 1
fi
