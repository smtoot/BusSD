<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatLock extends Model
{
    protected $fillable = [
        'trip_id',
        'passenger_id',
        'date_of_journey',
        'seats',
        'expires_at'
    ];

    protected $casts = [
        'seats' => 'array',
        'expires_at' => 'datetime'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }
}
