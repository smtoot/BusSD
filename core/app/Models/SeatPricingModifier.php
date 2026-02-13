<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SeatPricingModifier extends Model
{
    use GlobalStatus;
    
    protected $fillable = [
        'owner_id',
        'trip_id',
        'fleet_type_id',
        'name',
        'description',
        'modifier_type',
        'modifier_value',
        'applies_to',
        'seat_category',
        'seat_positions',
        'row_range_start',
        'row_range_end',
        'seat_type',
        'is_active',
        'priority',
    ];
    
    protected $casts = [
        'seat_positions' => 'array',
        'modifier_value' => 'decimal:2',
        'is_active' => 'boolean',
        'row_range_start' => 'integer',
        'row_range_end' => 'integer',
        'priority' => 'integer',
    ];
    
    // ==================
    // Relationships
    // ==================
    
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
    
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    
    public function fleetType()
    {
        return $this->belongsTo(FleetType::class);
    }
    
    // ==================
    // Business Logic
    // ==================
    
    /**
     * Check if this modifier applies to a specific seat
     *
     * @param string $seatNumber E.g., "1A", "12B"
     * @param string|null $seatType 'window', 'aisle', 'middle'
     * @return bool
     */
    public function appliesToSeat(string $seatNumber, ?string $seatType = null): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        // Extract row number and column from seat number
        if (!preg_match('/^(\d+)([A-Z]+)$/', $seatNumber, $matches)) {
            return false;
        }
        
        $row = (int) $matches[1];
        $column = $matches[2];
        
        switch ($this->applies_to) {
            case 'all':
                // Applies to all seats
                return true;
                
            case 'specific_seats':
                // Check if seat number is in the list
                return in_array($seatNumber, $this->seat_positions ?? []);
                
            case 'position':
                // Check row range if specified
                if ($this->row_range_start && $this->row_range_end) {
                    if ($row < $this->row_range_start || $row > $this->row_range_end) {
                        return false;
                    }
                }
                
                // Check seat type (window/aisle/middle) if specified
                if ($this->seat_type && $seatType) {
                    if ($this->seat_type !== $seatType) {
                        return false;
                    }
                }
                
                return true;
                
            case 'category':
                // Category-based logic (requires seat metadata from fleet layout)
                // This would need additional seat metadata in the fleet layout JSON
                // For now, return false - implement when adding category metadata
                return false;
                
            default:
                return false;
        }
    }
    
    /**
     * Apply this modifier to a base price
     *
     * @param float $basePrice
     * @return float The adjustment amount (can be positive or negative)
     */
    public function applyToPrice(float $basePrice): float
    {
        if ($this->modifier_type === 'percentage') {
            // Percentage modifier: +20% or -10%
            return ($basePrice * $this->modifier_value) / 100;
        }
        
        // Fixed amount modifier: +500 or -200
        return $this->modifier_value;
    }
    
    /**
     * Get scope name (trip-specific, fleet-specific, or global)
     *
     * @return string
     */
    public function getScopeAttribute(): string
    {
        if ($this->trip_id) {
            return 'trip';
        }
        
        if ($this->fleet_type_id) {
            return 'fleet_type';
        }
        
        return 'global';
    }
    
    /**
     * Get formatted modifier display
     *
     * @return string
     */
    public function getFormattedModifierAttribute(): string
    {
        if ($this->modifier_type === 'percentage') {
            $sign = $this->modifier_value >= 0 ? '+' : '';
            return "{$sign}{$this->modifier_value}%";
        }
        
        $sign = $this->modifier_value >= 0 ? '+' : '';
        return "{$sign}" . showAmount($this->modifier_value);
    }
    
    /**
     * Get applies to display text
     *
     * @return string
     */
    public function getAppliesToDisplayAttribute(): string
    {
        switch ($this->applies_to) {
            case 'all':
                return 'All Seats';
                
            case 'specific_seats':
                $count = count($this->seat_positions ?? []);
                return "{$count} Specific Seats";
                
            case 'position':
                $parts = [];
                
                if ($this->row_range_start && $this->row_range_end) {
                    $parts[] = "Rows {$this->row_range_start}-{$this->row_range_end}";
                }
                
                if ($this->seat_type) {
                    $parts[] = ucfirst($this->seat_type) . " Seats";
                }
                
                return implode(', ', $parts) ?: 'Position-Based';
                
            case 'category':
                return ucfirst($this->seat_category ?? 'Category-Based');
                
            default:
                return 'Unknown';
        }
    }
}
