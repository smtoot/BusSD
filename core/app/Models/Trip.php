<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use GlobalStatus;

    protected $casts = [
        'day_off' => 'array',
        'b2c_locked_seats' => 'array'
    ];

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
        return $this->belongsTo(Counter::class, 'starting_point');
    }

    public function destinationPoint()
    {
        return $this->belongsTo(Counter::class, 'destination_point');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
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
}
