<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use GlobalStatus;

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'feature_package');
    }
}
