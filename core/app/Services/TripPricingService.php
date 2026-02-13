<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\DynamicPricingRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TripPricingService
{
    /**
     * Calculate final price for a trip
     *
     * @param Trip $trip
     * @param array $options Additional options (occupancy, day_of_week, etc.)
     * @return float
     */
    public function calculatePrice(Trip $trip, array $options = []): float
    {
        $basePrice = $trip->price;
        
        // Apply dynamic pricing rules
        $modifiers = $this->getApplicableRules($trip);
        $finalPrice = $this->applyModifiers($basePrice, $modifiers, $options);
        
        return round($finalPrice, 2);
    }
    
    /**
     * Get pricing breakdown showing all components
     *
     * @param Trip $trip
     * @param array $options
     * @return array
     */
    public function getPriceBreakdown(Trip $trip, array $options = []): array
    {
        $basePrice = $trip->price;
        $modifiers = $this->getApplicableRules($trip);
        
        $breakdown = [
            'base_price' => $basePrice,
            'modifiers' => [],
            'final_price' => $basePrice,
        ];
        
        $currentPrice = $basePrice;
        
        foreach ($modifiers as $rule) {
            if (!$this->ruleConditionsMet($rule, $trip, $options)) {
                continue;
            }
            
            $adjustment = $this->calculateAdjustment($currentPrice, $rule);
            $breakdown['modifiers'][] = [
                'name' => $rule->name,
                'type' => $rule->modifier_type,
                'value' => $rule->modifier_value,
                'adjustment' => round($adjustment, 2),
            ];
            $currentPrice += $adjustment;
        }
        
        $breakdown['final_price'] = round($currentPrice, 2);
        
        return $breakdown;
    }
    
    /**
     * Get all pricing rules applicable to this trip
     *
     * @param Trip $trip
     * @return Collection
     */
    public function getApplicableRules(Trip $trip): Collection
    {
        return DynamicPricingRule::where('owner_id', $trip->owner_id)
            ->where('is_active', true)
            ->where(function($q) use ($trip) {
                // Route-specific or all routes
                $q->whereNull('route_id')
                  ->orWhere('route_id', $trip->route_id);
            })
            ->where(function($q) use ($trip) {
                // Time-based rules
                $q->whereNull('start_date')
                  ->orWhere(function($q2) use ($trip) {
                      $q2->where('start_date', '<=', $trip->date)
                         ->where('end_date', '>=', $trip->date);
                  });
            })
            ->orderBy('priority', 'asc')
            ->get();
    }
    
    /**
     * Apply pricing modifiers to base price
     *
     * @param float $basePrice
     * @param Collection $rules
     * @param array $options
     * @return float
     */
    protected function applyModifiers(float $basePrice, Collection $rules, array $options): float
    {
        $price = $basePrice;
        
        foreach ($rules as $rule) {
            // Check if rule conditions are met
            if (!$this->ruleConditionsMet($rule, null, $options)) {
                continue;
            }
            
            $price += $this->calculateAdjustment($price, $rule);
        }
        
        return $price;
    }
    
    /**
     * Calculate adjustment amount for a rule
     *
     * @param float $currentPrice
     * @param DynamicPricingRule $rule
     * @return float
     */
    protected function calculateAdjustment(float $currentPrice, $rule): float
    {
        if ($rule->modifier_type === 'percentage') {
            return ($currentPrice * $rule->modifier_value) / 100;
        }
        
        // Fixed amount
        return $rule->modifier_value;
    }
    
    /**
     * Check if rule conditions are met
     *
     * @param DynamicPricingRule $rule
     * @param Trip|null $trip
     * @param array $options
     * @return bool
     */
    protected function ruleConditionsMet($rule, $trip = null, array $options = []): bool
    {
        // Occupancy-based rules
        if ($rule->min_occupancy || $rule->max_occupancy) {
            $occupancy = $options['occupancy'] ?? null;
            
            if ($occupancy === null && $trip) {
                // Calculate current occupancy if trip provided
                $occupancy = $this->calculateOccupancy($trip);
            }
            
            if ($occupancy !== null) {
                if ($rule->min_occupancy && $occupancy < $rule->min_occupancy) {
                    return false;
                }
                
                if ($rule->max_occupancy && $occupancy > $rule->max_occupancy) {
                    return false;
                }
            }
        }
        
        // Day of week rules
        if ($rule->days_applicable) {
            $applicableDays = json_decode($rule->days_applicable, true) ?? [];
            
            if (!empty($applicableDays)) {
                $dayOfWeek = $options['day_of_week'] ?? null;
                
                if ($dayOfWeek === null && $trip) {
                    $dayOfWeek = date('N', strtotime($trip->date));
                }
                
                if ($dayOfWeek && !in_array($dayOfWeek, $applicableDays)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Calculate current occupancy percentage for a trip
     *
     * @param Trip $trip
     * @return float|null
     */
    protected function calculateOccupancy(Trip $trip): ?float
    {
        if (!$trip->fleet || !$trip->fleet->seat_layout) {
            return null;
        }
        
        $totalSeats = $trip->fleet->total_seat ?? 0;
        
        if ($totalSeats == 0) {
            return null;
        }
        
        // Count booked seats
        $bookedSeats = \App\Models\BookedTicket::where('trip_id', $trip->id)
            ->where('status', 1) // Only confirmed bookings
            ->sum('ticket_count');
        
        return ($bookedSeats / $totalSeats) * 100;
    }
    
    /**
     * Suggest optimal price based on rules and historical data
     *
     * @param Trip $trip
     * @return float
     */
    public function suggestPrice(Trip $trip): float
    {
        // Start with base price
        $suggestedPrice = $trip->price;
        
        // Get historical pricing for similar trips
        $historicalAvg = $this->getHistoricalAverage($trip);
        
        // Apply dynamic pricing rules
        $modifiers = $this->getApplicableRules($trip);
        $dynamicPrice = $this->applyModifiers($suggestedPrice, $modifiers, [
            'occupancy' => 50, // Assume 50% as baseline
            'day_of_week' => date('N', strtotime($trip->date)),
        ]);
        
        // Return weighted average (30% historical, 70% dynamic)
        return round(($historicalAvg * 0.3) + ($dynamicPrice * 0.7), 2);
    }
    
    /**
     * Get historical average price for similar trips
     *
     * @param Trip $trip
     * @return float
     */
    protected function getHistoricalAverage(Trip $trip): float
    {
        // Cache key
        $cacheKey = "historical_price_route_{$trip->route_id}_owner_{$trip->owner_id}";
        
        return Cache::remember($cacheKey, 3600, function() use ($trip) {
            $avg = Trip::where('route_id', $trip->route_id)
                ->where('owner_id', $trip->owner_id)
                ->where('date', '<', now())
                ->where('price', '>', 0)
                ->avg('price');
            
            return $avg ?? $trip->price;
        });
    }
    
    /**
     * Clear pricing cache for a route
     *
     * @param int $routeId
     * @param int $ownerId
     * @return void
     */
    public function clearCache(int $routeId, int $ownerId): void
    {
        $cacheKey = "historical_price_route_{$routeId}_owner_{$ownerId}";
        Cache::forget($cacheKey);
    }
    
    // ============================
    // Phase 2.2: Multi-Tier Seat Pricing
    // ============================
    
    /**
     * Calculate price for a specific seat with tier modifiers
     *
     * @param Trip $trip
     * @param string $seatNumber E.g., "1A", "12B"
     * @param string|null $seatType 'window', 'aisle', 'middle'
     * @return float
     */
    public function calculateSeatPrice(Trip $trip, string $seatNumber, ?string $seatType = null): float
    {
        $basePrice = $trip->price;
        
        // Get applicable seat pricing modifiers
        $modifiers = $this->getSeatPricingModifiers($trip, $seatNumber, $seatType);
        
        $finalPrice = $basePrice;
        
        foreach ($modifiers as $modifier) {
            $finalPrice += $modifier->applyToPrice($basePrice);
        }
        
        return round(max(0, $finalPrice), 2);
    }
    
    /**
     * Get seat pricing modifiers for a specific seat
     *
     * @param Trip $trip
     * @param string $seatNumber
     * @param string|null $seatType
     * @return \Illuminate\Support\Collection
     */
    protected function getSeatPricingModifiers(Trip $trip, string $seatNumber, ?string $seatType = null)
    {
        return \App\Models\SeatPricingModifier::where('owner_id', $trip->owner_id)
            ->where(function($q) use ($trip) {
                // Trip-specific OR fleet-type-specific OR global
                $q->where('trip_id', $trip->id)
                  ->orWhere(function($q2) use ($trip) {
                      $q2->where('fleet_type_id', $trip->fleet_type_id)
                         ->whereNull('trip_id');
                  })
                  ->orWhere(function($q3) {
                      $q3->whereNull('trip_id')
                         ->whereNull('fleet_type_id');
                  });
            })
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(function($modifier) use ($seatNumber, $seatType) {
                return $modifier->appliesToSeat($seatNumber, $seatType);
            });
    }
    
    /**
     * Get pricing breakdown for all seats in a trip
     *
     * @param Trip $trip
     * @return array
     */
    public function getAllSeatsPricing(Trip $trip): array
    {
        $fleet = $trip->fleet;
        
        if (!$fleet || !$fleet->seat_layout) {
            return [];
        }
        
        $seatLayout = json_decode($fleet->seat_layout, true);
        $seats = $seatLayout['seats'] ?? [];
        
        $pricing = [];
        
        foreach ($seats as $seat) {
            $seatNumber = $seat['label'] ?? null;
            
            if (!$seatNumber) {
                continue;
            }
            
            // Determine seat type from layout position
            $seatType = $this->determineSeatType($seat, $seats);
            
            $seatPrice = $this->calculateSeatPrice($trip, $seatNumber, $seatType);
            
            $pricing[$seatNumber] = [
                'base_price' => (float) $trip->price,
                'final_price' => $seatPrice,
                'seat_type' => $seatType,
                'discount' => $seatPrice < $trip->price ? round($trip->price - $seatPrice, 2) : 0,
                'premium' => $seatPrice > $trip->price ? round($seatPrice - $trip->price, 2) : 0,
            ];
        }
        
        return $pricing;
    }
    
    /**
     * Determine seat type (window/aisle/middle) from layout position
     *
     * @param array $seat
     * @param array $allSeats
     * @return string
     */
    protected function determineSeatType(array $seat, array $allSeats): string
    {
        $seatNumber = $seat['label'] ?? '';
        
        // Extract row number
        if (!preg_match('/^(\d+)([A-Z]+)$/', $seatNumber, $matches)) {
            return 'middle';
        }
        
        $row = $matches[1];
        $column = $matches[2];
        
        // Get all seats in the same row
        $rowSeats = array_filter($allSeats, function($s) use ($row) {
            return str_starts_with($s['label'] ?? '', $row . '');
        });
        
        if (empty($rowSeats)) {
            return 'middle';
        }
        
        // Sort by column letter
        usort($rowSeats, function($a, $b) {
            return strcmp($a['label'] ?? '', $b['label'] ?? '');
        });
        
        // Find position of current seat
        $seatIndex = null;
        foreach ($rowSeats as $index => $rowSeat) {
            if (($rowSeat['label'] ?? '') === $seatNumber) {
                $seatIndex = $index;
                break;
            }
        }
        
        if ($seatIndex === null) {
            return 'middle';
        }
        
        $totalInRow = count($rowSeats);
        
        // First or last seat in row = window
        if ($seatIndex === 0 || $seatIndex === $totalInRow - 1) {
            return 'window';
        }
        
        // Middle seats (simplified - could be enhanced with aisle detection)
        return 'middle';
    }
}
