<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchOperatingHours extends Model
{
    protected $table = 'branch_operating_hours';

    protected $fillable = [
        'branch_id',
        'day_of_week',
        'opens_at',
        'closes_at',
        'is_24_hours',
        'is_closed'
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_24_hours' => 'boolean',
        'is_closed' => 'boolean',
        'opens_at' => 'datetime:H:i',
        'closes_at' => 'datetime:H:i'
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Helper Methods
    public function getDayName()
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$this->day_of_week] ?? 'Unknown';
    }

    public function isOpen()
    {
        if ($this->is_closed) {
            return false;
        }

        if ($this->is_24_hours) {
            return true;
        }

        $now = now()->format('H:i');
        return $now >= $this->opens_at && $now <= $this->closes_at;
    }
}
