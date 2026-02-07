<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use GlobalStatus;

    protected $casts = [
        'stoppages' => 'array'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function startingPoint()
    {
        return $this->belongsTo(Counter::class, 'starting_point');
    }

    public function destinationPoint()
    {
        return $this->belongsTo(Counter::class, 'destination_point');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function ticketPrice()
    {
        return $this->hasMany(TicketPrice::class);
    }

    public function bookedTickets()
    {
        return $this->hasManyThrough(BookedTicket::class, Trip::class)->where('booked_tickets.status', '1');
    }

    public function canceledTickets()
    {
        return $this->hasMany(BookedTicket::class , Trip::class)->where('booked_tickets.status', '0');
    }

}
