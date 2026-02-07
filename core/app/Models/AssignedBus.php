<?php

namespace App\Models;

use App\Models\Owner;
use App\Models\Trip;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class AssignedBus extends Model
{
    use GlobalStatus;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }
}
