<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchRevenue extends Model
{
    protected $table = 'branch_revenue';

    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'trip_id',
        'booking_id',
        'revenue_amount',
        'revenue_type',
        'split_percentage',
        'booking_date'
    ];

    protected $casts = [
        'revenue_amount' => 'decimal:8',
        'split_percentage' => 'decimal:2',
        'booking_date' => 'date'
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Helper Methods
    public function isPrimaryRevenue()
    {
        return $this->revenue_type === 'primary';
    }

    public function isSharedRevenue()
    {
        return $this->revenue_type === 'shared';
    }
}
