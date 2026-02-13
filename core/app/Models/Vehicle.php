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

    // Branch relationships for multi-branch fleet management
    public function primaryBranch()
    {
        return $this->belongsTo(Branch::class, 'primary_branch_id');
    }

    public function currentBranch()
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    public function isInPool()
    {
        return $this->is_pooled;
    }

    public function assignToBranch(Branch $branch, $isPrimary = false)
    {
        if ($isPrimary) {
            $this->primary_branch_id = $branch->id;
        }
        $this->current_branch_id = $branch->id;
        $this->save();
        return $this;
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
