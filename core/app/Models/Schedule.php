<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use GlobalStatus;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
