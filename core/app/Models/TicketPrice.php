<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class TicketPrice extends Model
{
    use GlobalStatus;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }

    public function prices()
    {
        return $this->hasMany(TicketPriceByStoppage::class);
    }
}
