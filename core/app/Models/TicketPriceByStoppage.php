<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriceByStoppage extends Model
{
    protected $casts = [
        'source_destination' => 'array'
    ];

    public function mainPrice()
    {
        return $this->belongsTo(TicketPrice::class, 'ticket_price_id');
    }
}
