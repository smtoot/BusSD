<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'passenger_id', 'trip_id', 'date_of_journey', 
        'seat_count', 'pickup_id', 'destination_id', 'status'
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function pickup()
    {
        return $this->belongsTo(BoardingPoint::class, 'pickup_id');
    }

    public function destination()
    {
        return $this->belongsTo(DroppingPoint::class, 'destination_id');
    }
}
