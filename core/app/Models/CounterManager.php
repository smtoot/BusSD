<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CounterManager extends Authenticatable
{
    use GlobalStatus, HasPermissions;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'permissions' => 'array',
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

    public function counter()
    {
        return $this->hasOne(Counter::class);
    }

    public function tickets()
    {
        return $this->hasMany(BookedTicket::class);
    }

    public function bookedTickets()
    {
        return $this->hasMany(BookedTicket::class)->whereStatus('1');
    }

    public function canceledTickets()
    {
        return $this->hasMany(BookedTicket::class)->whereStatus('0');
    }
}
