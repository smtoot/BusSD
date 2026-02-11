<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use GlobalStatus;

    protected $casts = [
        'photos' => 'array',
        'is_vip' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }

    public function assignedBuses()
    {
        return $this->hasMany(AssignedBus::class);
    }

    public function trip()
    {
        return $this->hasOne(Trip::class);
    }

    /**
     * Vehicle amenities (built-in features)
     */
    public function amenities()
    {
        return $this->belongsToMany(AmenityTemplate::class, 'vehicle_amenities')
            ->where('amenity_type', 'vehicle')
            ->withTimestamps();
    }

    /**
     * Get total capacity (use total_seats if available, fallback to fleet type)
     */
    public function capacity()
    {
        if ($this->total_seats) {
            return $this->total_seats;
        }
        
        if ($this->fleetType && $this->fleetType->deck_seats) {
            return $this->fleetType->deck_seats;
        }
        
        return 30; // Default fallback
    }
}
