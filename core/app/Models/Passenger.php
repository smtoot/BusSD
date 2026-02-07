<?php

namespace App\Models;

use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Passenger extends Authenticatable
{
    use HasApiTokens, SoftDeletes, UserNotify;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'phone_otp',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'ver_code_send_at' => 'datetime'
    ];

    public function bookedTickets()
    {
        return $this->hasMany(BookedTicket::class);
    }

    public function getMobileNumberAttribute()
    {
        return $this->dial_code . $this->mobile;
    }
}
