<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use GlobalStatus;

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'starting_city_id');
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
