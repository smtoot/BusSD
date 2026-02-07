<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripRating extends Model
{

    public function bookedTicket()
    {
        return $this->belongsTo(BookedTicket::class, 'booked_ticket_id');
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
