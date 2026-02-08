# Seat Template Builder - Code Review Audit

**Date:** 2026-02-07  
**Reviewer:** Kilo Code (Review Mode)  
**Component:** Admin Seat Layout Visual Builder  
**Status:** NEEDS CHANGES

---

## Executive Summary

The seat template builder provides Admin users with a visual drag-and-drop interface to create seat layout templates with grid-based positioning. Owners can only view and use these templates, not create them. The implementation includes a visual builder with element types (seat, driver, door, toilet, aisle) and stores layout data as JSON.

**Overall Assessment:** The feature has a solid foundation but contains **CRITICAL** issues that prevent it from functioning. The most severe issue is an empty migration file that fails to add the required database columns.

---

## Issues Found

| Severity | File:Line | Issue |
|----------|-----------|-------|
| CRITICAL | `core/database/migrations/2026_02_07_210151_add_schema_to_seat_layouts.php:14` | Migration file is empty - `schema` column never added to database |
| CRITICAL | `core/app/Http/Controllers/Admin/FleetController.php:76` | No validation that `schema` column exists before storing JSON |
| CRITICAL | `core/resources/views/admin/fleet/seat_layouts.blade.php:310` | Using blocking `prompt()` for user input - poor UX |
| WARNING | `core/app/Models/SeatLayout.php:12` | No JSON schema validation - malformed data could cause errors |
| WARNING | `core/resources/views/admin/fleet/seat_layouts.blade.php:291` | No null check for `$layout->schema` before accessing properties |
| WARNING | `core/app/Http/Controllers/Admin/FleetController.php:61-64` | No validation for JSON structure or required fields |
| WARNING | `core/resources/views/admin/fleet/seat_layouts.blade.php:119-127` | No limits on grid size - could cause performance issues |
| SUGGESTION | `core/app/Http/Controllers/Owner/FleetController.php:71-80` | Inconsistent - CoOwner uses old `layout` field, Admin uses `schema` |
| SUGGESTION | `core/resources/views/admin/fleet/seat_layouts.blade.php:309-313` | No validation for seat label uniqueness |
| SUGGESTION | `core/database/migrations/2026_02_07_210151_add_schema_to_seat_layouts.php:22` | Empty `down()` method - no proper rollback path |

---

## Critical Issues (Must Fix)

### 1. Empty Migration File

**File:** `core/database/migrations/2026_02_07_210151_add_schema_to_seat_layouts.php:14`  
**Confidence:** 100%  
**Severity:** CRITICAL

**Problem:** The migration file is completely empty. The `schema` column was never added to the `seat_layouts` table, which means the visual builder will fail when trying to store JSON schema data.

**Current Code:**
```php
public function up(): void
{
    Schema::table('seat_layouts', function (Blueprint $table) {
        //
    });
}
```

**Impact:** 
- Feature cannot function at all
- Database errors when attempting to save seat layouts
- All Admin seat layout operations will fail

**Recommended Fix:**
```php
public function up(): void
{
    Schema::table('seat_layouts', function (Blueprint $table) {
        $table->string('name')->nullable()->after('layout');
        $table->json('schema')->nullable()->after('name');
    });
}

public function down(): void
{
    Schema::table('seat_layouts', function (Blueprint $table) {
        $table->dropColumn(['name', 'schema']);
    });
}
```

**Action Required:**
1. Update the migration file with the code above
2. Run the migration: `php artisan migrate`
3. Verify columns exist in database

---

### 2. No Validation for Schema Column

**File:** `core/app/Http/Controllers/Admin/FleetController.php:76`  
**Confidence:** 95%  
**Severity:** CRITICAL

**Problem:** The controller attempts to store JSON to a `schema` column that doesn't exist in the database. This will cause a database error.

**Current Code:**
```php
public function seatLayoutStore(Request $request, $id = 0)
{
    $request->validate([
        'name'   => 'required|string|max:40',
        'schema' => 'required|json'
    ]);

    // ...
    $seatLayout->schema = json_decode($request->schema);
    $seatLayout->save();
}
```

**Impact:**
- Database query will fail with "Unknown column 'schema'" error
- User receives generic error message
- No proper error handling

**Recommended Fix:**
```php
public function seatLayoutStore(Request $request, $id = 0)
{
    $request->validate([
        'name'   => 'required|string|max:40',
        'schema' => 'required|json'
    ]);

    // Validate JSON structure
    $schema = json_decode($request->schema, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $notify[] = ['error', 'Invalid JSON format'];
        return back()->withNotify($notify);
    }

    if (!isset($schema['meta']['grid']['rows']) || !isset($schema['layout'])) {
        $notify[] = ['error', 'Invalid schema structure'];
        return back()->withNotify($notify);
    }

    // Validate grid size limits
    if ($schema['meta']['grid']['rows'] > 50 || $schema['meta']['grid']['cols'] > 20) {
        $notify[] = ['error', 'Grid size exceeds limits (max 50 rows, 20 cols)'];
        return back()->withNotify($notify);
    }

    if ($id) {
        $seatLayout = SeatLayout::findOrFail($id);
        $message    = 'Seat layout template updated successfully';
    } else {
        $seatLayout = new SeatLayout();
        $message    = 'Seat layout template created successfully';
    }

    $seatLayout->owner_id = 0; // Admin Managed
    $seatLayout->name     = $request->name;
    $seatLayout->schema   = $schema; // Store as array, Laravel will cast to JSON
    $seatLayout->save();

    $notify[] = ['success', $message];
    return back()->withNotify($notify);
}
```

---

### 3. Poor UX with Blocking prompt()

**File:** `core/resources/views/admin/fleet/seat_layouts.blade.php:310`  
**Confidence:** 90%  
**Severity:** CRITICAL

**Problem:** Using JavaScript's `prompt()` is blocking, not user-friendly, and doesn't match the application's UI patterns. It also doesn't provide validation or good user experience.

**Current Code:**
```javascript
cell.on('click', function() {
    const x = $(this).data('x');
    const y = $(this).data('y');
    
    if ($(this).hasClass('active-element')) {
        // Remove element
        $(this).removeClass('active-element').empty();
        layoutData = layoutData.filter(item => !(item.x === x && item.y === y));
    } else {
        // Add element
        let label = "";
        if(currentType === 'seat') {
            label = prompt("Enter Seat Label (e.g. A1, B2):") || "Seat";
        }
        applyElement($(this), currentType, label);
        layoutData.push({x, y, type: currentType, label: label});
    }
    updateSchemaField();
});
```

**Impact:**
- Poor user experience with blocking dialogs
- No validation for seat labels
- Doesn't match application's UI patterns
- Difficult to use on mobile devices
- No way to cancel or skip

**Recommended Fix:**

**Step 1: Add input field to modal**
```html
<div class="form-group" id="seatLabelGroup" style="display:none;">
    <label>@lang('Seat Label')</label>
    <div class="input-group">
        <input type="text" id="seatLabelInput" class="form-control" 
               placeholder="@lang('e.g. A1, B2')" maxlength="10">
        <button type="button" class="btn btn--primary" id="addSeatBtn">
            <i class="las la-plus"></i> @lang('Add')
        </button>
    </div>
    <small class="text-muted">@lang('Enter a unique label for this seat')</small>
</div>
```

**Step 2: Update JavaScript logic**
```javascript
let pendingCell = null;

cell.on('click', function() {
    const x = $(this).data('x');
    const y = $(this).data('y');
    
    if ($(this).hasClass('active-element')) {
        // Remove element
        $(this).removeClass('active-element').empty();
        layoutData = layoutData.filter(item => !(item.x === x && item.y === y));
        updateSchemaField();
    } else {
        // Add element
        if(currentType === 'seat') {
            pendingCell = $(this);
            $('#seatLabelGroup').show();
            $('#seatLabelInput').val('').focus();
        } else {
            // Non-seat elements don't need labels
            applyElement($(this), currentType, '');
            layoutData.push({x, y, type: currentType, label: ''});
            updateSchemaField();
        }
    }
});

$('#addSeatBtn').on('click', function() {
    const label = $('#seatLabelInput').val().trim();
    
    if (!label) {
        alert('Please enter a seat label');
        return;
    }
    
    // Check for duplicate labels
    const existingLabel = layoutData.find(item => item.label === label);
    if (existingLabel) {
        alert('Seat label already exists!');
        return;
    }
    
    if (pendingCell) {
        applyElement(pendingCell, currentType, label);
        const x = pendingCell.data('x');
        const y = pendingCell.data('y');
        layoutData.push({x, y, type: currentType, label: label});
        updateSchemaField();
        
        $('#seatLabelGroup').hide();
        pendingCell = null;
    }
});

// Allow Enter key to add seat
$('#seatLabelInput').on('keypress', function(e) {
    if (e.which === 13) {
        $('#addSeatBtn').click();
    }
});
```

---

## Warning Issues (Should Fix)

### 4. No JSON Schema Validation

**File:** `core/app/Models/SeatLayout.php:12`  
**Confidence:** 85%  
**Severity:** WARNING

**Problem:** The model casts `schema` to object but doesn't validate the structure. Malformed JSON or unexpected structure could cause errors throughout the application.

**Current Code:**
```php
class SeatLayout extends Model
{
    use GlobalStatus;
    
    protected $casts = [
        'schema' => 'object'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
```

**Recommended Fix:**
```php
class SeatLayout extends Model
{
    use GlobalStatus;
    
    protected $casts = [
        'schema' => 'object'
    ];

    protected $fillable = [
        'owner_id',
        'name',
        'layout',
        'schema',
        'status'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function getHasVisualMappingAttribute()
    {
        return !empty($this->schema) && 
               isset($this->schema->meta) && 
               isset($this->schema->layout);
    }

    public function getGridRowsAttribute()
    {
        return $this->schema->meta->grid->rows ?? 10;
    }

    public function getGridColsAttribute()
    {
        return $this->schema->meta->grid->cols ?? 5;
    }
}
```

---

### 5. No Null Check for Schema

**File:** `core/resources/views/admin/fleet/seat_layouts.blade.php:291`  
**Confidence:** 85%  
**Severity:** WARNING

**Problem:** The code accesses `$schema.meta.grid.rows` without checking if `schema` is null first. This will cause JavaScript errors for layouts without visual mapping.

**Current Code:**
```javascript
if(schema) {
    rows = schema.meta.grid.rows;
    cols = schema.meta.grid.cols;
    layoutData = schema.layout;
}
```

**Recommended Fix:**
```javascript
if(schema && schema.meta && schema.meta.grid) {
    rows = schema.meta.grid.rows || 10;
    cols = schema.meta.grid.cols || 5;
    layoutData = schema.layout || [];
} else {
    rows = 10;
    cols = 5;
    layoutData = [];
}
```

---

### 6. No Validation for JSON Structure

**File:** `core/app/Http/Controllers/Admin/FleetController.php:61-64`  
**Confidence:** 85%  
**Severity:** WARNING

**Problem:** The controller only validates that the input is JSON, but doesn't validate the structure or required fields.

**Current Code:**
```php
$request->validate([
    'name'   => 'required|string|max:40',
    'schema' => 'required|json'
]);
```

**Recommended Fix:**
```php
$request->validate([
    'name'   => 'required|string|max:40',
    'schema' => 'required|json'
]);

$schema = json_decode($request->schema, true);

// Validate JSON structure
if (!isset($schema['meta']['version']) || 
    !isset($schema['meta']['grid']) || 
    !isset($schema['layout'])) {
    $notify[] = ['error', 'Invalid schema structure'];
    return back()->withNotify($notify);
}

// Validate required fields
if (!isset($schema['meta']['grid']['rows']) || 
    !isset($schema['meta']['grid']['cols'])) {
    $notify[] = ['error', 'Grid dimensions are required'];
    return back()->withNotify($notify);
}

// Validate grid size
if ($schema['meta']['grid']['rows'] < 1 || $schema['meta']['grid']['rows'] > 50) {
    $notify[] = ['error', 'Rows must be between 1 and 50'];
    return back()->withNotify($notify);
}

if ($schema['meta']['grid']['cols'] < 1 || $schema['meta']['grid']['cols'] > 20) {
    $notify[] = ['error', 'Columns must be between 1 and 20'];
    return back()->withNotify($notify);
}

// Validate layout array
if (!is_array($schema['layout'])) {
    $notify[] = ['error', 'Layout must be an array'];
    return back()->withNotify($notify);
}

// Validate each layout item
foreach ($schema['layout'] as $item) {
    if (!isset($item['x']) || !isset($item['y']) || !isset($item['type'])) {
        $notify[] = ['error', 'Each layout item must have x, y, and type'];
        return back()->withNotify($notify);
    }
}
```

---

### 7. No Grid Size Limits

**File:** `core/resources/views/admin/fleet/seat_layouts.blade.php:119-127`  
**Confidence:** 80%  
**Severity:** WARNING

**Problem:** Users can enter any grid size, which could cause performance issues or browser crashes with extremely large grids.

**Current Code:**
```javascript
$('#resizeGrid').on('click', function() {
    rows = parseInt($('#gridRows').val()) || 10;
    cols = parseInt($('#gridCols').val()) || 5;
    renderGrid();
});
```

**Recommended Fix:**
```javascript
$('#resizeGrid').on('click', function() {
    rows = parseInt($('#gridRows').val()) || 10;
    cols = parseInt($('#gridCols').val()) || 5;
    
    // Enforce limits
    rows = Math.min(Math.max(rows, 1), 50);
    cols = Math.min(Math.max(cols, 1), 20);
    
    // Update inputs to reflect limits
    $('#gridRows').val(rows);
    $('#gridCols').val(cols);
    
    renderGrid();
});

// Also validate on input change
$('#gridRows, #gridCols').on('input', function() {
    let val = parseInt($(this).val());
    const max = $(this).attr('id') === 'gridRows' ? 50 : 20;
    const min = 1;
    
    if (val > max) {
        $(this).val(max);
    } else if (val < min || isNaN(val)) {
        $(this).val(min);
    }
});
```

---

## Suggestion Issues (Nice to Have)

### 8. Inconsistent Implementation

**File:** `core/app/Http/Controllers/Owner/FleetController.php:71-80`  
**Confidence:** 75%  
**Severity:** SUGGESTION

**Problem:** CoOwner controller still uses the old `layout` field while Admin uses the new `schema` field. This creates inconsistency across the application.

**Current Code (Owner):**
```php
public function layoutStore(Request $request, $id = 0)
{
    $notify[] = ['error', 'Manual layout creation is disabled. Please use Admin templates.'];
    return back()->withNotify($notify);
}
```

**Current Code (CoOwner):**
```php
public function layoutStore(Request $request, $id = 0)
{
    $request->validate([
        'layout' => 'required|string|max:40'
    ]);

    if ($id) {
        $seatLayout = SeatLayout::findOrFail($id);
        $message    = 'Seat layout updated successfully';
    } else {
        $seatLayout = new SeatLayout();
        $message    = 'Seat layout created successfully';
    }
    $seatLayout->owner_id = authUser('co-owner')->owner->id;
    $seatLayout->layout   = $request->layout;
    $seatLayout->save();

    $notify[] = ['success', $message];
    return back()->withNotify($notify);
}
```

**Recommended Fix:** 
1. Update CoOwner to also use the visual builder (recommended for consistency)
2. OR document that CoOwner uses the old text-based layout system
3. Consider deprecating the old `layout` field in favor of `schema`

---

### 9. No Seat Label Uniqueness Validation

**File:** `core/resources/views/admin/fleet/seat_layouts.blade.php:309-313`  
**Confidence:** 70%  
**Severity:** SUGGESTION

**Problem:** Multiple seats can have the same label, which could cause confusion or issues in booking systems.

**Current Code:**
```javascript
if(currentType === 'seat') {
    label = prompt("Enter Seat Label (e.g. A1, B2):") || "Seat";
}
applyElement($(this), currentType, label);
layoutData.push({x, y, type: currentType, label: label});
```

**Recommended Fix:**
```javascript
// Check if label already exists
const existingLabel = layoutData.find(item => 
    item.label === label && (item.x !== x || item.y !== y)
);
if (existingLabel) {
    alert('Seat label "' + label + '" already exists!');
    return;
}

// Also validate label format
if (!/^[A-Z]\d+$/.test(label)) {
    alert('Seat label must be in format like A1, B2, C3');
    return;
}
```

---

### 10. Missing Database Column for 'name'

**File:** Multiple files reference `$seatLayout->name`  
**Confidence:** 75%  
**Severity:** SUGGESTION

**Problem:** The code references a `name` column that may not exist in the database.

**Files affected:**
- `core/resources/views/admin/fleet/seat_layouts.blade.php:22`
- `core/resources/views/owner/fleet_type/index.blade.php:25`
- `core/resources/views/owner/seat_layout/index.blade.php:19`

**Recommended Fix:** Ensure the migration adds both `name` and `schema` columns (covered in Critical Issue #1).

---

## Architecture Review

### Strengths

1. **Separation of Concerns:** Clear separation between Admin (creates templates) and Owner (uses templates)
2. **JSON Storage:** Using JSON for flexible schema storage is appropriate
3. **Visual Builder:** Good UX concept for creating seat layouts
4. **Element Types:** Support for multiple element types (seat, driver, door, toilet, aisle)
5. **Grid System:** Grid-based positioning is intuitive for users

### Weaknesses

1. **No Migration Implementation:** Critical - database schema not updated
2. **Inconsistent Data Model:** Mix of old `layout` field and new `schema` field
3. **Poor Input Validation:** Minimal validation on both frontend and backend
4. **Blocking UI:** Using `prompt()` instead of proper UI components
5. **No Error Handling:** Limited error handling for edge cases
6. **No Undo/Redo:** Users cannot undo mistakes in the builder
7. **No Preview:** No way to preview the layout before saving
8. **No Templates:** No pre-built templates for common layouts

---

## Security Considerations

### Potential Issues

1. **JSON Injection:** No validation of JSON structure could lead to injection attacks
2. **XSS Risk:** User input in seat labels not properly sanitized
3. **No CSRF Protection:** Form has CSRF token but no additional validation
4. **No Rate Limiting:** No protection against rapid form submissions

### Recommendations

```php
// Add XSS protection
$sanitizedLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

// Add rate limiting (in routes)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('seat-layouts/store/{id?}', 'seatLayoutStore');
});
```

---

## Performance Considerations

### Potential Issues

1. **Large Grids:** No limits on grid size could cause performance issues
2. **DOM Manipulation:** Frequent DOM updates could be slow
3. **No Caching:** Layout data not cached for repeated access
4. **N+1 Queries:** Potential N+1 query issues when loading layouts

### Recommendations

1. Implement grid size limits (50 rows, 20 cols)
2. Use virtual scrolling for large grids
3. Cache layout data in browser localStorage
4. Add database indexes on frequently queried fields
5. Use eager loading to prevent N+1 queries

---

## Testing Recommendations

### Unit Tests Needed

1. **SeatLayout Model:**
   - Test schema casting
   - Test validation methods
   - Test relationship methods

2. **FleetController:**
   - Test seatLayoutStore with valid data
   - Test seatLayoutStore with invalid JSON
   - Test seatLayoutStore with oversized grid
   - Test seatLayoutStatus method

### Integration Tests Needed

1. **Admin Flow:**
   - Create seat layout via builder
   - Edit existing seat layout
   - Enable/disable seat layout
   - Delete seat layout

2. **Owner Flow:**
   - View available templates
   - Select template for fleet type
   - Verify template appears in booking

### E2E Tests Needed

1. **Full User Journey:**
   - Admin creates template
   - Owner creates fleet type with template
   - Passenger books seat from template
   - Verify seat selection matches template

---

## Migration Path

### Phase 1: Critical Fixes (Immediate)

1. ✅ Fix empty migration file
2. ✅ Run migration to add database columns
3. ✅ Add backend validation for JSON structure
4. ✅ Replace `prompt()` with proper UI input

### Phase 2: Warning Fixes (High Priority)

1. Add null checks for schema access
2. Implement grid size limits
3. Add JSON schema validation
4. Improve error handling

### Phase 3: Suggestions (Medium Priority)

1. Standardize CoOwner implementation
2. Add seat label uniqueness validation
3. Implement undo/redo functionality
4. Add pre-built templates

### Phase 4: Enhancements (Low Priority)

1. Add preview mode
2. Implement drag-and-drop reordering
3. Add layout export/import
4. Create template gallery

---

## Conclusion

The seat template builder has a solid conceptual foundation and provides a good user experience for Admin users to create seat layout templates. However, **critical issues prevent it from functioning at all**.

**Must Fix Before Production:**
1. Empty migration file (CRITICAL)
2. Backend validation for schema column (CRITICAL)
3. Replace blocking `prompt()` with proper UI (CRITICAL)

**Should Fix Soon:**
1. Add null checks and validation
2. Implement grid size limits
3. Improve error handling

**Nice to Have:**
1. Standardize implementation across Admin/Owner/CoOwner
2. Add seat label validation
3. Implement undo/redo functionality

**Overall Recommendation:** **NEEDS CHANGES**

The feature cannot be used until the critical issues are resolved. Once fixed, it will provide a valuable tool for Admin users to manage seat layout templates across the platform.

---

## Appendix: Code Snippets

### Fixed Migration File

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seat_layouts', function (Blueprint $table) {
            $table->string('name')->nullable()->after('layout');
            $table->json('schema')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_layouts', function (Blueprint $table) {
            $table->dropColumn(['name', 'schema']);
        });
    }
};
```

### Enhanced Controller Validation

```php
public function seatLayoutStore(Request $request, $id = 0)
{
    $request->validate([
        'name'   => 'required|string|max:40',
        'schema' => 'required|json'
    ]);

    $schema = json_decode($request->schema, true);
    
    // Validate JSON structure
    if (json_last_error() !== JSON_ERROR_NONE) {
        $notify[] = ['error', 'Invalid JSON format'];
        return back()->withNotify($notify);
    }

    // Validate required fields
    if (!isset($schema['meta']['grid']['rows']) || 
        !isset($schema['meta']['grid']['cols']) || 
        !isset($schema['layout'])) {
        $notify[] = ['error', 'Invalid schema structure'];
        return back()->withNotify($notify);
    }

    // Validate grid size
    if ($schema['meta']['grid']['rows'] > 50 || 
        $schema['meta']['grid']['rows'] < 1) {
        $notify[] = ['error', 'Rows must be between 1 and 50'];
        return back()->withNotify($notify);
    }

    if ($schema['meta']['grid']['cols'] > 20 || 
        $schema['meta']['grid']['cols'] < 1) {
        $notify[] = ['error', 'Columns must be between 1 and 20'];
        return back()->withNotify($notify);
    }

    if ($id) {
        $seatLayout = SeatLayout::findOrFail($id);
        $message    = 'Seat layout template updated successfully';
    } else {
        $seatLayout = new SeatLayout();
        $message    = 'Seat layout template created successfully';
    }

    $seatLayout->owner_id = 0; // Admin Managed
    $seatLayout->name     = $request->name;
    $seatLayout->schema   = $schema;
    $seatLayout->save();

    $notify[] = ['success', $message];
    return back()->withNotify($notify);
}
```

---

**Document Version:** 1.0  
**Last Updated:** 2026-02-07  
**Next Review:** After critical fixes are implemented
