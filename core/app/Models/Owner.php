<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Owner extends Authenticatable
{
    use UserNotify;

    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'general_settings'  => 'object',
        'ver_code_send_at'  => 'datetime'
    ];

    public function loginLogs()
    {
        return $this->hasMany(OwnerLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function soldPackages()
    {
        return $this->hasMany(SoldPackage::class);
    }

    public function seatLayouts()
    {
        return $this->hasMany(SeatLayout::class);
    }

    public function fleetTypes()
    {
        return $this->hasMany(FleetType::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
    public function supervisors()
    {
        return $this->hasMany(Supervisor::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function assignedBuses()
    {
        return $this->hasMany(AssignedBus::class);
    }

    public function coAdmins()
    {
        return $this->hasMany(CoOwner::class);
    }

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

    public function counterManagers()
    {
        return $this->hasMany(CounterManager::class);
    }

    public function ticketPrices()
    {
        return $this->hasMany(TicketPrice::class);
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

    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function bookedTickets()
    {
        return $this->hasMany(BookedTicket::class)->where('booked_tickets.status', '1');
    }

    public function canceledTickets()
    {
        return $this->hasMany(BookedTicket::class)->where('booked_tickets.status', '0');
    }

    public function scopeActivePackages()
    {
        return $this->hasMany(SoldPackage::class)->whereStatus('1')->where('ends_at', '>', Carbon::now())->orderByDesc('ends_at')->get();
    }

    public function boughtPackages()
    {
        return $this->hasMany(SoldPackage::class)->where('status', '!=', '0')->get();
    }
}
