<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDroppingPoint extends Model
{
    protected $fillable = [
        'trip_id',
        'dropping_point_id',
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

    public function droppingPoint()
    {
        return $this->belongsTo(DroppingPoint::class);
    }
}
