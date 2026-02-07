<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class BookedTicket extends Model
{
    use GlobalStatus;

    protected $casts = [
        'seats'              => 'array',
        'passenger_details'  => 'array',
        'source_destination' => 'array'
    ];

    public function counterManager()
    {
        return $this->belongsTo(CounterManager::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }
}
