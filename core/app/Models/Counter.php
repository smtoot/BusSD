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
        $order = "CASE id ";
        $i = 0;
        foreach ($array as $id) {
            $order .= "WHEN $id THEN $i ";
            $i++;
        }
        $order .= "END";

        return $query->whereIn('id', $array)
        ->orderByRaw($order)->get();
    }
}
