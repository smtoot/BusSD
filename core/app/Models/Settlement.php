<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $fillable = [
        'owner_id',
        'gross_amount',
        'commission_amount',
        'net_amount',
        'status',
        'trx',
        'settlement_period'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
