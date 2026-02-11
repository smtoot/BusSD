<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Driver extends Authenticatable
{
    use GlobalStatus, HasPermissions;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => $this->dial_code . $this->mobile,
        );
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function assignedBuses()
    {
        return $this->hasMany(AssignedBus::class);
    }
}
