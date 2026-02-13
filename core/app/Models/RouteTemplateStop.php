<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteTemplateStop extends Model
{
    protected $fillable = [
        'route_template_id',
        'city_id',
        'sequence_order',
        'time_offset_minutes',
        'dwell_time_minutes',
        'distance_from_previous',
        'boarding_allowed',
        'dropping_allowed',
        'notes',
    ];
    
    protected $casts = [
        'sequence_order' => 'integer',
        'time_offset_minutes' => 'integer',
        'dwell_time_minutes' => 'integer',
        'distance_from_previous' => 'decimal:2',
        'boarding_allowed' => 'boolean',
        'dropping_allowed' => 'boolean',
    ];
    
    // ==================
    // Relationships
    // ==================
    
    public function template()
    {
        return $this->belongsTo(RouteTemplate::class, 'route_template_id');
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    // ==================
    // Accessors
    // ==================
    
    /**
     * Get formatted time offset (e.g., "2h 30m")
     *
     * @return string
     */
    public function getFormattedTimeOffsetAttribute(): string
    {
        $hours = floor($this->time_offset_minutes / 60);
        $minutes = $this->time_offset_minutes % 60;
        
        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }
        
        return "{$minutes}m";
    }
    
    /**
     * Get formatted dwell time (e.g., "15m")
     *
     * @return string
     */
    public function getFormattedDwellTimeAttribute(): string
    {
        return "{$this->dwell_time_minutes}m";
    }
    
    /**
     * Whether boarding or dropping is allowed
     *
     * @return string
     */
    public function getAllowedActionsAttribute(): string
    {
        if ($this->boarding_allowed && $this->dropping_allowed) {
            return 'Both';
        }
        
        if ($this->boarding_allowed) {
            return 'Boarding Only';
        }
        
        if ($this->dropping_allowed) {
            return 'Dropping Only';
        }
        
        return 'None';
    }
}
