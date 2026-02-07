<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SoldPackage extends Model
{
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function scopePaymentIncomplete()
    {
        return $this->where('status', Status::PAYMENT_INCOMPLETE);
    }

    public function scopeActive()
    {
        return $this->where('status', Status::SOLD_PACKAGE_ACTIVE);
    }

    public function scopeExpired()
    {
        return $this->where('status', Status::SOLD_PACKAGE_EXPIRED);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::ENABLE) {
            $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans('Expired') . '</span>';
        }
        return $html;
    }
}
