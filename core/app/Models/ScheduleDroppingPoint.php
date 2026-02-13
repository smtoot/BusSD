<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleDroppingPoint extends Model
{
    protected $fillable = [
        'schedule_id',
        'dropping_point_id',
        'time_offset_minutes',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'time_offset_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function droppingPoint()
    {
        return $this->belongsTo(DroppingPoint::class);
    }

    /**
     * Calculate actual time based on schedule start time
     */
    public function getActualTimeAttribute()
    {
        if (!$this->schedule) {
            return null;
        }
        
        $startsFrom = \Carbon\Carbon::parse($this->schedule->starts_from);
        return $startsFrom->addMinutes($this->time_offset_minutes)->format('H:i');
    }
}
