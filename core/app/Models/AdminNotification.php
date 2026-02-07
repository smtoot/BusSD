<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    public function owner()
    {
    	return $this->belongsTo(Owner::class);
    }
}
