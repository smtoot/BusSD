<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use GlobalStatus;

    public function soldPackages()
    {
        return $this->hasMany(SoldPackage::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_package');
    }
}
