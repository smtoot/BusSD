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
        'starting_point',
        'destination_point',
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
        return $this->belongsTo(Counter::class, 'starting_point');
    }

    public function destinationPoint()
    {
        return $this->belongsTo(Counter::class, 'destination_point');
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
}
