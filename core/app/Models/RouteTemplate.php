<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class RouteTemplate extends Model
{
    use GlobalStatus;
    
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'base_route_id',
        'total_duration_minutes',
        'total_distance_km',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'total_duration_minutes' => 'integer',
        'total_distance_km' => 'decimal:2',
    ];
    
    // ==================
    // Relationships
    // ==================
    
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    
    public function baseRoute()
    {
        return $this->belongsTo(Route::class, 'base_route_id');
    }
    
    public function stops()
    {
        return $this->hasMany(RouteTemplateStop::class)->orderBy('sequence_order');
    }
    
    // ==================
    // Business Logic
    // ==================
    
    /**
     * Recalculate total duration and distance based on stops
     */
    public function recalculateTotals(): void
    {
        $lastStop = $this->stops()->orderBy('sequence_order', 'desc')->first();
        
        $this->total_duration_minutes = $lastStop ? $lastStop->time_offset_minutes : 0;
        $this->total_distance_km = $this->stops()->sum('distance_from_previous') ?? 0;
        $this->save();
    }
    
    /**
     * Apply this template to a trip (create boarding/dropping points)
     *
     * @param Trip $trip
     * @return void
     */
    public function applyToTrip(Trip $trip): void
    {
        // Clear existing points if any
        $trip->boardingPoints()->delete();
        $trip->droppingPoints()->delete();
        
        foreach ($this->stops as $stop) {
            // Create boarding point if allowed
            if ($stop->boarding_allowed) {
                TripBoardingPoint::create([
                    'trip_id' => $trip->id,
                    'city_id' => $stop->city_id,
                    'sequence_order' => $stop->sequence_order,
                    'time_offset' => $stop->time_offset_minutes,
                    'dwell_time' => $stop->dwell_time_minutes,
                ]);
            }
            
            // Create dropping point if allowed
            if ($stop->dropping_allowed) {
                TripDroppingPoint::create([
                    'trip_id' => $trip->id,
                    'city_id' => $stop->city_id,
                    'sequence_order' => $stop->sequence_order,
                    'time_offset' => $stop->time_offset_minutes,
                ]);
            }
        }
    }
    
    /**
     * Get formatted total duration (e.g., "5h 30m")
     *
     * @return string
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->total_duration_minutes / 60);
        $minutes = $this->total_duration_minutes % 60;
        
        if ($hours > 0) {
            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }
        
        return "{$minutes}m";
    }
    
    /**
     * Get stop count
     *
     * @return int
     */
    public function getStopCountAttribute(): int
    {
        return $this->stops()->count();
    }
}
