<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function method()
    {
        return $this->belongsTo(WithdrawalMethod::class, 'method_id');
    }

    public function statusBadge()
    {
        $html = '';
        if ($this->status == 0) {
            $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        } elseif ($this->status == 1) {
            $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
        } elseif ($this->status == 2) {
            $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
        }
        return $html;
    }
}
