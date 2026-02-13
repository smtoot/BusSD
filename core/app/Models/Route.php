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
        return $this->belongsTo(City::class, 'starting_city_id');
    }

    public function destinationPoint()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
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

    public function boardingPoints()
    {
        return $this->belongsToMany(BoardingPoint::class, 'route_boarding_points')
            ->withPivot('pickup_time_offset', 'sort_order')
            ->orderBy('pivot_sort_order');
    }

    public function droppingPoints()
    {
        return $this->belongsToMany(DroppingPoint::class, 'route_dropping_points')
            ->withPivot('dropoff_time_offset', 'sort_order')
            ->orderBy('pivot_sort_order');
    }

    public function dynamicPricingRules()
    {
        return $this->hasMany(DynamicPricingRule::class);
    }
}
