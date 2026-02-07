<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class SeatLayout extends Model
{
    use GlobalStatus;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
