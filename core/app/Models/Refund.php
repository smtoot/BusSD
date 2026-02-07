<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{

    public function bookedTicket()
    {
        return $this->belongsTo(BookedTicket::class, 'booked_ticket_id');
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id');
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
