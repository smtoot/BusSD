<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use GlobalStatus;

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
}
