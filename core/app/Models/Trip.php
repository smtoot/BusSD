<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use GlobalStatus;

    protected $casts = [
        'app_locked_seats' => 'array',
        'amenities' => 'array',
        'date' => 'date'
    ];

    protected $fillable = [
        'owner_id',
        'title',
        'fleet_type_id',
        'route_id',
        'schedule_id',
        'starting_city_id',
        'destination_city_id',

        'app_locked_seats',
        'status',
        'trip_type',
        'trip_category',
        'bus_type',
        'base_price',
        'weekend_surcharge',
        'holiday_surcharge',
        'early_bird_discount',
        'last_minute_surcharge',
        'search_priority',
        'trip_status',
        'date',
        'route_template_id',
    ];

    /**
     * Trip Types
     */
    const TRIP_TYPE_EXPRESS = 'express';
    const TRIP_TYPE_SEMI_EXPRESS = 'semi_express';
    const TRIP_TYPE_LOCAL = 'local';
    const TRIP_TYPE_NIGHT = 'night';

    /**
     * Trip Categories
     */
    const TRIP_CATEGORY_PREMIUM = 'premium';
    const TRIP_CATEGORY_STANDARD = 'standard';
    const TRIP_CATEGORY_BUDGET = 'budget';

    /**
     * Trip Status (Workflow)
     */
    const TRIP_STATUS_DRAFT = 'draft';
    const TRIP_STATUS_PENDING = 'pending';
    const TRIP_STATUS_APPROVED = 'approved';
    const TRIP_STATUS_ACTIVE = 'active';

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function bookedTickets()
    {
        return $this->hasMany(BookedTicket::class)->whereStatus('1');
    }

    public function seatLocks()
    {
        return $this->hasMany(SeatLock::class);
    }

    public function canceledTickets()
    {
        return $this->hasMany(BookedTicket::class)->whereStatus('0');
    }

    public function assignedBuses()
    {
        return $this->hasMany(AssignedBus::class);
    }

    public function startingPoint()
    {
        return $this->belongsTo(City::class, 'starting_city_id');
    }

    public function destinationPoint()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function cancellationPolicy()
    {
        return $this->belongsTo(CancellationPolicy::class);
    }

    // Branch relationships for multi-branch operations
    public function owningBranch()
    {
        return $this->belongsTo(Branch::class, 'owning_branch_id');
    }

    public function originBranch()
    {
        return $this->belongsTo(Branch::class, 'origin_branch_id');
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    /**
     * Trip Amenities Relationship (trip-specific options like meals, snacks)
     */
    public function amenities()
    {
        return $this->hasMany(TripAmenity::class);
    }

    /**
     * Get vehicle amenities (built-in features) from assigned vehicle
     */
    public function getVehicleAmenities()
    {
        if (!$this->vehicle_id || !$this->vehicle) {
            return collect([]);
        }
        
        return $this->vehicle->amenities;
    }

    /**
     * Get trip options (service offerings) from trip amenities
     * Filters for amenities marked as 'trip' type
     */
    public function getTripOptions()
    {
        return $this->amenities;
    }

    /**
     * Get all amenities (vehicle + trip combined)
     * Used for display to passengers
     */
    public function getAllAmenities()
    {
        $vehicleAmenities = $this->getVehicleAmenities();
        $tripOptions = $this->getTripOptions();
        
        // Combine both collections
        // Vehicle amenities are AmenityTemplate models
        // Trip options are TripAmenity models (need to resolve)
        return $vehicleAmenities->merge($tripOptions);
    }

    /**
     * Scope: Active trips (status = 1 AND trip_status = active)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('trip_status', self::TRIP_STATUS_ACTIVE);
    }

    /**
     * Scope: Draft trips
     */
    public function scopeDraft($query)
    {
        return $query->where('trip_status', self::TRIP_STATUS_DRAFT);
    }

    /**
     * Scope: Pending approval
     */
    public function scopePending($query)
    {
        return $query->where('trip_status', self::TRIP_STATUS_PENDING);
    }

    /**
     * Scope: Approved trips
     */
    public function scopeApproved($query)
    {
        return $query->where('trip_status', self::TRIP_STATUS_APPROVED);
    }

    public function scopeWithActiveFleetType($query)
    {
        return $query->with('fleetType', function ($fleetType) {
            $fleetType->active();
        });
    }

    public function scopeWithActiveVehicle($query)
    {
        return $query->with('fleetType.vehicles', function ($vehicle) {
            $vehicle->active();
        });
    }

    /**
     * Calculate final price based on trip pricing rules
     */
    public function calculatePrice($basePrice = null, $bookingTime = null)
    {
        $price = $basePrice ?? $this->getBasePrice();

        if (!$price) {
            return 0;
        }

        $bookingTime = $bookingTime ?? now();

        // Apply weekend surcharge
        if ($this->weekend_surcharge > 0 && $this->isWeekend($bookingTime)) {
            $price += $price * ($this->weekend_surcharge / 100);
        }

        // Apply holiday surcharge
        if ($this->holiday_surcharge > 0 && $this->isHoliday($bookingTime)) {
            $price += $price * ($this->holiday_surcharge / 100);
        }

        // Apply early bird discount
        if ($this->early_bird_discount > 0) {
            $tripDeparture = $this->getNextDeparture();
            if ($tripDeparture && $tripDeparture->diffInHours($bookingTime) >= 24) {
                $price -= $price * ($this->early_bird_discount / 100);
            }
        }

        // Apply last minute surcharge
        if ($this->last_minute_surcharge > 0) {
            $tripDeparture = $this->getNextDeparture();
            if ($tripDeparture && $tripDeparture->diffInHours($bookingTime) < 6) {
                $price += $price * ($this->last_minute_surcharge / 100);
            }
        }

        return max(0, round($price, 2));
    }

    /**
     * Get base price from trip or ticket price
     */
    public function getBasePrice()
    {
        // If trip has base_price, use it
        if ($this->base_price) {
            return $this->base_price;
        }

        // Otherwise, get from TicketPrice table
        $ticketPrice = TicketPrice::where('owner_id', $this->owner_id)
            ->where('route_id', $this->route_id)
            ->where('fleet_type_id', $this->fleet_type_id)
            ->where('status', 1)
            ->first();

        return $ticketPrice ? $ticketPrice->main_price : 0;
    }

    /**
     * Calculate commission amount
     */
    public function calculateCommission($price)
    {
        $commissionRate = $this->owner->commission_rate ?? gs('app_commission', 10);
        return round($price * ($commissionRate / 100), 2);
    }

    /**
     * Calculate net revenue (price - commission)
     */
    public function calculateNetRevenue($price)
    {
        return $price - $this->calculateCommission($price);
    }

    /**
     * Get next departure time for this trip
     */
    public function getNextDeparture()
    {
        return $this->departure_datetime;
    }

    /**
     * Check if given date is a day off
     */
    public function isDayOff($date)
    {
        return false;
    }

    /**
     * Check if given date is weekend
     */
    public function isWeekend($date)
    {
        $dayOfWeek = $date->dayOfWeek;
        return in_array($dayOfWeek, [0, 6]); // Sunday or Saturday
    }

    /**
     * Check if given date is holiday (placeholder - needs holiday calendar integration)
     */
    public function isHoliday($date)
    {
        // TODO: Integrate with holiday calendar
        return false;
    }

    /**
     * Get trip type label
     */
    public function getTripTypeLabelAttribute()
    {
        $labels = [
            self::TRIP_TYPE_EXPRESS => 'Express',
            self::TRIP_TYPE_SEMI_EXPRESS => 'Semi-Express',
            self::TRIP_TYPE_LOCAL => 'Local',
            self::TRIP_TYPE_NIGHT => 'Night Service',
        ];
        return $labels[$this->trip_type] ?? 'Unknown';
    }

    /**
     * Get trip category label
     */
    public function getTripCategoryLabelAttribute()
    {
        $labels = [
            self::TRIP_CATEGORY_PREMIUM => 'Premium',
            self::TRIP_CATEGORY_STANDARD => 'Standard',
            self::TRIP_CATEGORY_BUDGET => 'Budget',
        ];
        return $labels[$this->trip_category] ?? 'Unknown';
    }

    /**
     * Get trip status label
     */
    public function getTripStatusLabelAttribute()
    {
        $labels = [
            self::TRIP_STATUS_DRAFT => 'Draft',
            self::TRIP_STATUS_PENDING => 'Pending Approval',
            self::TRIP_STATUS_APPROVED => 'Approved',
            self::TRIP_STATUS_ACTIVE => 'Active',
        ];
        return $labels[$this->trip_status] ?? 'Unknown';
    }

    /**
     * Get trip status badge HTML
     */
    public function getTripStatusBadgeAttribute()
    {
        $badges = [
            self::TRIP_STATUS_DRAFT => '<span class="badge badge--secondary">Draft</span>',
            self::TRIP_STATUS_PENDING => '<span class="badge badge--warning">Pending</span>',
            self::TRIP_STATUS_APPROVED => '<span class="badge badge--info">Approved</span>',
            self::TRIP_STATUS_ACTIVE => '<span class="badge badge--success">Active</span>',
        ];
        return $badges[$this->trip_status] ?? '<span class="badge badge--secondary">Unknown</span>';
    }

    public function boardedCount()
    {
        return $this->bookedTickets()->where('is_boarded', 1)->sum('ticket_count');
    }

    public function bookedCount()
    {
        return $this->bookedTickets()->sum('ticket_count');
    }

    public function fleetCapacity()
    {
        $capacity = $this->fleetType->deck_seats ?? 0;
        if ($capacity == 0 && $this->fleetType->seats) {
            $capacity = is_array($this->fleetType->seats) ? count($this->fleetType->seats) : 0;
        }
        return $capacity > 0 ? $capacity : 30; // Fallback to 30 if still 0
    }

    public function checkinProgress()
    {
        $booked = $this->bookedCount();
        if ($booked == 0) return 0;
        return round(($this->boardedCount() / $booked) * 100);
    }

    /**
     * Get applicable dynamic pricing rules for this trip
     */
    public function getApplicablePricingRules()
    {
        return DynamicPricingRule::active()
            ->where(function ($query) {
                $query->where('owner_id', $this->owner_id)
                      ->orWhere('owner_id', 0);
            })
            ->where(function ($query) {
                $query->where('route_id', $this->route_id)
                      ->orWhereNull('route_id');
            })
            ->where(function ($query) {
                $query->where('fleet_type_id', $this->fleet_type_id)
                      ->orWhereNull('fleet_type_id');
            })
            ->validForDate($this->date ?? now())
            ->validForDayOfWeek(($this->date ?? now())->dayOfWeek)
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->get()
            ->filter(function ($rule) {
                return $rule->isApplicable($this);
            });
    }

    /**
     * Calculate final price with dynamic pricing
     */
    public function calculateDynamicPrice($basePrice = null)
    {
        $price = $basePrice ?? $this->getBasePrice();
        
        // Apply applicable dynamic pricing rules
        $applicableRules = $this->getApplicablePricingRules();
        
        foreach ($applicableRules as $rule) {
            $price = $rule->applyToPrice($price);
        }
        
        return max(0, round($price, 2));
    }

    // ============================
    // Pricing Service Integration (Phase 2.1)
    // ============================

    /**
     * Get calculated price using centralized pricing service
     * This applies all dynamic pricing rules
     */
    public function getCalculatedPriceAttribute(): float
    {
        return app(\App\Services\TripPricingService::class)->calculatePrice($this);
    }

    /**
     * Get detailed pricing breakdown showing base price and all modifiers
     */
    public function getPricingBreakdown(array $options = []): array
    {
        return app(\App\Services\TripPricingService::class)->getPriceBreakdown($this, $options);
    }

    /**
     * Get suggested optimal price based on historical data and rules
     */
    public function getSuggestedPrice(): float
    {
        return app(\App\Services\TripPricingService::class)->suggestPrice($this);
    }
}
