<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class DynamicPricingRule extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'owner_id',
        'route_id',
        'fleet_type_id',
        'name',
        'rule_type',
        'operator',
        'value',
        'min_hours_before_departure',
        'max_hours_before_departure',
        'applicable_days',
        'applicable_dates',
        'start_time',
        'end_time',
        'min_seats_available',
        'max_seats_available',
        'is_active',
        'valid_from',
        'valid_until',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'applicable_days' => 'array',
        'applicable_dates' => 'array',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Rule Types
     */
    const RULE_TYPE_SURGE = 'surge';
    const RULE_TYPE_EARLY_BIRD = 'early_bird';
    const RULE_TYPE_LAST_MINUTE = 'last_minute';
    const RULE_TYPE_WEEKEND = 'weekend';
    const RULE_TYPE_HOLIDAY = 'holiday';
    const RULE_TYPE_CUSTOM = 'custom';

    /**
     * Operators
     */
    const OPERATOR_PERCENTAGE = 'percentage';
    const OPERATOR_FIXED = 'fixed';

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidForDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
              ->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', $date);
        });
    }

    public function scopeValidForDayOfWeek($query, $dayOfWeek)
    {
        return $query->where(function ($q) use ($dayOfWeek) {
            $q->whereNull('applicable_days')
              ->orWhereJsonContains('applicable_days', $dayOfWeek);
        });
    }

    public function scopeValidForTime($query, $time)
    {
        return $query->where(function ($q) use ($time) {
            $q->whereNull('start_time')
              ->orWhere('start_time', '<=', $time);
        })->where(function ($q) use ($time) {
            $q->whereNull('end_time')
              ->orWhere('end_time', '>=', $time);
        });
    }

    public function scopeValidForSeats($query, $seatsAvailable)
    {
        return $query->where(function ($q) use ($seatsAvailable) {
            $q->whereNull('min_seats_available')
              ->orWhere('min_seats_available', '<=', $seatsAvailable);
        })->where(function ($q) use ($seatsAvailable) {
            $q->whereNull('max_seats_available')
              ->orWhere('max_seats_available', '>=', $seatsAvailable);
        });
    }

    public function getRuleTypeLabelAttribute()
    {
        $labels = [
            self::RULE_TYPE_SURGE => 'Surge Pricing',
            self::RULE_TYPE_EARLY_BIRD => 'Early Bird Discount',
            self::RULE_TYPE_LAST_MINUTE => 'Last Minute Surcharge',
            self::RULE_TYPE_WEEKEND => 'Weekend Surcharge',
            self::RULE_TYPE_HOLIDAY => 'Holiday Surcharge',
            self::RULE_TYPE_CUSTOM => 'Custom Rule',
        ];
        return $labels[$this->rule_type] ?? 'Unknown';
    }

    public function getOperatorLabelAttribute()
    {
        return $this->operator === self::OPERATOR_PERCENTAGE ? 'Percentage' : 'Fixed Amount';
    }

    /**
     * Apply pricing rule to base price
     */
    public function applyToPrice($basePrice)
    {
        if ($this->operator === self::OPERATOR_PERCENTAGE) {
            return $basePrice + ($basePrice * ($this->value / 100));
        }
        return $basePrice + $this->value;
    }

    /**
     * Check if rule is applicable to given trip
     */
    public function isApplicable($trip)
    {
        // Check date validity
        $tripDate = $trip->date ?? $trip->created_at;
        if (!$this->isValidForDate($tripDate)) {
            return false;
        }

        // Check day of week
        if (!$this->isValidForDayOfWeek($tripDate->dayOfWeek)) {
            return false;
        }

        // Check time
        if ($trip->departure_time && !$this->isValidForTime($trip->departure_time)) {
            return false;
        }

        // Check seats availability
        $seatsAvailable = $trip->fleetCapacity() - $trip->bookedCount();
        if (!$this->isValidForSeats($seatsAvailable)) {
            return false;
        }

        // Check hours before departure for time-based rules
        if (in_array($this->rule_type, [self::RULE_TYPE_EARLY_BIRD, self::RULE_TYPE_LAST_MINUTE])) {
            $hoursBeforeDeparture = $tripDate->diffInHours(now());
            if ($this->min_hours_before_departure && $hoursBeforeDeparture < $this->min_hours_before_departure) {
                return false;
            }
            if ($this->max_hours_before_departure && $hoursBeforeDeparture > $this->max_hours_before_departure) {
                return false;
            }
        }

        return true;
    }
}
