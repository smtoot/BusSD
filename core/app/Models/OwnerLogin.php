<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerLogin extends Model
{
    protected $guarded = ['id'];


    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
