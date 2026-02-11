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

    protected $casts = [
        'license_expiry_date' => 'date',
        'permissions' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('license_expiry_date', '>', now())
            ->where('license_expiry_date', '<=', now()->addDays(30));
    }

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
