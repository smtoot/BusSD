<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use GlobalStatus;

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function routes()
    {
        return $this->belongsTo(Route::class);
    }

    public function counterManager()
    {
        return $this->belongsTo(CounterManager::class);
    }

    public function scopeRouteStoppages($query, $array)
    {
        return $query->whereIn('id', $array)
        ->orderByRaw("field(id,".implode(',',$array).")")->get();
    }
}
