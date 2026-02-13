<?php

namespace App\Services;

use App\Models\Route;
use App\Models\Trip;
use App\Models\BookedTicket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RouteAnalyticsService
{
    /**
     * Get comprehensive profitability analysis for a route
     *
     * @param Route $route
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getRouteProfitability(Route $route, $startDate, $endDate): array
    {
        $ownerId = $route->owner_id;
        
        // Get all trips for this route in period
        $trips = Trip::where('route_id', $route->id)
            ->where('owner_id', $ownerId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        
        $tripIds = $trips->pluck('id');
        
        // Calculate metrics
        $revenue = $this->calculateRevenue($tripIds);
        $costs = $this->calculateCosts($trips, $route);
        $occupancy = $this->calculateOccupancy($trips);
        $trends = $this->calculateTrends($route->id, $ownerId, $startDate, $endDate);
        
        // Calculate profit
        $grossProfit = $revenue['net'] - $costs['total'];
        $profitMargin = $revenue['net'] > 0 
            ? ($grossProfit / $revenue['net']) * 100 
            : 0;
        
        // Generate recommendations
        $recommendations = $this->generateRecommendations($route, $revenue, $costs, $occupancy, $profitMargin);
        
        return [
            'route' => [
                'id' => $route->id,
                'name' => $route->title,
                'distance_km' => $route->distance,
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'days' => now()->parse($startDate)->diffInDays($endDate) + 1,
            ],
            'revenue' => $revenue,
            'costs' => $costs,
            'profit' => [
                'gross_profit' => round($grossProfit, 2),
                'profit_margin' => round($profitMargin, 2),
                'profit_per_trip' => $trips->count() > 0
                    ? round($grossProfit / $trips->count(), 2)
                    : 0,
                'profit_per_km' => $route->distance > 0 && $trips->count() > 0
                    ? round($grossProfit / ($route->distance * $trips->count()), 2)
                    : 0,
            ],
            'occupancy' => $occupancy,
            'operational' => [
                'total_trips' => $trips->count(),
                'completed_trips' => $trips->where('status', 1)->count(),
                'cancelled_trips' => $trips->where('status', 0)->count(),
                'cancellation_rate' => $trips->count() > 0
                    ? round(($trips->where('status', 0)->count() / $trips->count()) * 100, 2)
                    : 0,
            ],
            'trends' => $trends,
            'recommendations' => $recommendations,
        ];
    }
    
    /**
     * Calculate revenue metrics
     *
     * @param Collection $tripIds
     * @return array
     */
    protected function calculateRevenue(Collection $tripIds): array
    {
        if ($tripIds->isEmpty()) {
            return [
                'gross' => 0,
                'platform_fees' => 0,
                'net' => 0,
                'total_bookings' => 0,
                'avg_ticket_price' => 0,
            ];
        }
        
        $bookings = BookedTicket::whereIn('trip_id', $tripIds)
            ->where('status', 1) // Confirmed bookings only
            ->get();
        
        $grossRevenue = $bookings->sum('amount');
        
        // Platform fee: 5% (can be made configurable)
        $platformFees = $grossRevenue * 0.05;
        
        return [
            'gross' => round($grossRevenue, 2),
            'platform_fees' => round($platformFees, 2),
            'net' => round($grossRevenue - $platformFees, 2),
            'total_bookings' => $bookings->count(),
            'avg_ticket_price' => $bookings->count() > 0 
                ? round($bookings->avg('amount'), 2) 
                : 0,
        ];
    }
    
    /**
     * Calculate cost metrics
     *
     * @param Collection $trips
     * @param Route $route
     * @return array
     */
    protected function calculateCosts(Collection $trips, Route $route): array
    {
        $tripCount = $trips->count();
        
        if ($tripCount === 0) {
            return [
                'fuel' => 0,
                'driver' => 0,
                'maintenance' => 0,
                'total' => 0,
            ];
        }
        
        $totalDistance = $route->distance * $tripCount;
        
        // Cost estimates (TODO: Make these configurable per owner)
        $fuelCostPerKm = 0.50; // SAR per km
        $driverCostPerTrip = 200; // SAR per trip
        $maintenanceCostPerKm = 0.10; // SAR per km
        
        $fuelCost = $totalDistance * $fuelCostPerKm;
        $driverCost = $tripCount * $driverCostPerTrip;
        $maintenanceCost = $totalDistance * $maintenanceCostPerKm;
        
        return [
            'fuel' => round($fuelCost, 2),
            'driver' => round($driverCost, 2),
            'maintenance' => round($maintenanceCost, 2),
            'total' => round($fuelCost + $driverCost + $maintenanceCost, 2),
        ];
    }
    
    /**
     * Calculate occupancy metrics
     *
     * @param Collection $trips
     * @return array
     */
    protected function calculateOccupancy(Collection $trips): array
    {
        $totalSeatsAvailable = 0;
        $totalSeatsSold = 0;
        
        foreach ($trips as $trip) {
            $capacity = $trip->fleet->total_seat ?? 0;
            $sold = BookedTicket::where('trip_id', $trip->id)
                ->where('status', 1)
                ->sum('ticket_count');
            
            $totalSeatsAvailable += $capacity;
            $totalSeatsSold += $sold;
        }
        
        $avgOccupancy = $totalSeatsAvailable > 0
            ? ($totalSeatsSold / $totalSeatsAvailable) * 100
            : 0;
        
        return [
            'total_seats_available' => $totalSeatsAvailable,
            'total_seats_sold' => $totalSeatsSold,
            'empty_seats' => $totalSeatsAvailable - $totalSeatsSold,
            'avg_occupancy_rate' => round($avgOccupancy, 2),
        ];
    }
    
    /**
     * Calculate revenue/occupancy trends over time
     *
     * @param int $routeId
     * @param int $ownerId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    protected function calculateTrends(int $routeId, int $ownerId, $startDate, $endDate): array
    {
        // Group by date for daily trends
        $dailyData = Trip::where('route_id', $routeId)
            ->where('owner_id', $ownerId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                'date',
                DB::raw('COUNT(*) as trip_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Format for chart
        return $dailyData->map(function($item) {
            return [
                'date' => $item->date,
                'trips' => $item->trip_count,
            ];
        })->toArray();
    }
    
    /**
     * Generate actionable recommendations
     *
     * @param Route $route
     * @param array $revenue
     * @param array $costs
     * @param array $occupancy
     * @param float $profitMargin
     * @return array
     */
    protected function generateRecommendations(Route $route, array $revenue, array $costs, array $occupancy, float $profitMargin): array
    {
        $recommendations = [];
        
        // Low occupancy recommendation
        if ($occupancy['avg_occupancy_rate'] < 50 && $occupancy['avg_occupancy_rate'] > 0) {
            $recommendations[] = [
                'type' => 'warning',
                'category' => 'occupancy',
                'title' => 'Low Occupancy Rate',
                'message' => "Average occupancy is {$occupancy['avg_occupancy_rate']}%. Consider dynamic pricing or marketing campaigns.",
                'action' => 'Reduce prices by 10-15% during low-demand periods',
                'priority' => 'medium',
            ];
        }
        
        // High empty seats recommendation
        if ($occupancy['total_seats_available'] > 0) {
            $emptyRate = ($occupancy['empty_seats'] / $occupancy['total_seats_available']) * 100;
            
            if ($emptyRate > 50) {
                $recommendations[] = [
                    'type' => 'danger',
                    'category' => 'capacity',
                    'title' => 'High Empty Seat Count',
                    'message' => "{$occupancy['empty_seats']} seats remain unsold (" . round($emptyRate, 1) . "%). Significant revenue loss.",
                    'action' => 'Implement last-minute discount strategy',
                    'priority' => 'high',
                ];
            }
        }
        
        // Profitability recommendations
        if ($profitMargin > 30) {
            $recommendations[] = [
                'type' => 'success',
                'category' => 'performance',
                'title' => 'High Profitability',
                'message' => "Profit margin is " . round($profitMargin, 1) . "%. This route is performing excellently.",
                'action' => 'Consider adding more trips on this route',
                'priority' => 'low',
            ];
        } elseif ($profitMargin >= 0 && $profitMargin < 10) {
            $recommendations[] = [
                'type' => 'warning',
                'category' => 'performance',
                'title' => 'Low Profit Margin',
                'message' => "Profit margin is only " . round($profitMargin, 1) . "%. Review pricing and costs.",
                'action' => 'Increase ticket prices or optimize operational costs',
                'priority' => 'medium',
            ];
        } elseif ($profitMargin < 0) {
            $recommendations[] = [
                'type' => 'danger',
                'category' => 'performance',
                'title' => 'Unprofitable Route',
                'message' => "This route is losing money (margin: " . round($profitMargin, 1) . "%). Immediate action required.",
                'action' => 'Suspend route or significantly adjust pricing/frequency',
                'priority' => 'critical',
            ];
        }
        
        // High cancellation rate
        if (isset($revenue['total_bookings']) && $revenue['total_bookings'] > 0) {
            // Can add cancellation analysis here if needed
        }
        
        return $recommendations;
    }
    
    /**
     * Compare multiple routes side-by-side
     *
     * @param array $routeIds
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function compareRoutes(array $routeIds, $startDate, $endDate): array
    {
        $comparison = [];
        
        foreach ($routeIds as $routeId) {
            $route = Route::find($routeId);
            if (!$route) continue;
            
            $data = $this->getRouteProfitability($route, $startDate, $endDate);
            
            $comparison[] = [
                'route_id' => $route->id,
                'route_name' => $route->title,
                'profit' => $data['profit']['gross_profit'],
                'margin' => $data['profit']['profit_margin'],
                'occupancy' => $data['occupancy']['avg_occupancy_rate'],
                'revenue' => $data['revenue']['net'],
                'trips' => $data['operational']['total_trips'],
            ];
        }
        
        // Sort by profit (descending)
        usort($comparison, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
        
        return $comparison;
    }
    
    /**
     * Get top performing routes
     *
     * @param int $ownerId
     * @param int $limit
     * @param string $metric
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function getTopRoutes(int $ownerId, int $limit = 5, string $metric = 'profit', $startDate = null, $endDate = null): Collection
    {
        $startDate = $startDate ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');
        
        $routes = Route::where('owner_id', $ownerId)->active()->get();
        
        if ($routes->isEmpty()) {
            return collect([]);
        }
        
        $routePerformance = $routes->map(function($route) use ($startDate, $endDate, $metric) {
            $data = $this->getRouteProfitability($route, $startDate, $endDate);
            
            $score = match($metric) {
                'profit' => $data['profit']['gross_profit'],
                'occupancy' => $data['occupancy']['avg_occupancy_rate'],
                'revenue' => $data['revenue']['net'],
                default => $data['profit']['gross_profit'],
            };
            
            return [
                'route' => $route,
                'score' => $score,
                'data' => $data,
            ];
        });
        
        return $routePerformance->sortByDesc('score')->take($limit)->values();
    }
}
