<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class FleetType extends Model
{
    use GlobalStatus;

    protected $casts = [
        'seats' => 'object'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function seatLayout()
    {
        return $this->belongsTo(SeatLayout::class);
    }
}
