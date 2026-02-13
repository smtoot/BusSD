<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'owner_id',
        'name',
        'route_id',
        'starting_city_id',
        'destination_city_id',
        'fleet_type_id',
        'vehicle_id',
        'starts_from',
        'ends_at',
        'duration_hours',
        'duration_minutes',
        'recurrence_type',
        'recurrence_days',
        'starts_on',
        'ends_on',
        'never_ends',
        'base_price',
        'inventory_allocation',
        'inventory_count',
        'cancellation_policy',
        'cancellation_policy_id',
        'trip_type',
        'trip_category',
        'bus_type',
        'weekend_surcharge',
        'holiday_surcharge',
        'early_bird_discount',
        'last_minute_surcharge',
        'search_priority',
        'trip_status',
        'amenities',
    ];

    protected $casts = [
        'recurrence_days' => 'array',
        'never_ends'     => 'boolean',
        'amenities'      => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function startingPoint()
    {
        return $this->belongsTo(City::class, 'starting_city_id');
    }

    public function destinationPoint()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    // Phase 1.2: Template-level point management
    public function scheduleBoardingPoints()
    {
        return $this->hasMany(\App\Models\ScheduleBoardingPoint::class)->orderBy('sort_order');
    }

    public function scheduleDroppingPoints()
    {
        return $this->hasMany(\App\Models\ScheduleDroppingPoint::class)->orderBy('sort_order');
    }

    // Convenience methods for accessing points with full details
    public function boardingPointsWithDetails()
    {
        return $this->scheduleBoardingPoints()->with('boardingPoint.city');
    }

    public function droppingPointsWithDetails()
    {
        return $this->scheduleDroppingPoints()->with('droppingPoint.city');
    }
}
