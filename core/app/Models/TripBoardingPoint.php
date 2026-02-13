<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripBoardingPoint extends Model
{
    protected $fillable = [
        'trip_id',
        'boarding_point_id',
        'scheduled_time',
        'actual_time',
        'passenger_count',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime:H:i:s',
        'actual_time' => 'datetime:H:i:s',
        'passenger_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function boardingPoint()
    {
        return $this->belongsTo(BoardingPoint::class);
    }
}
