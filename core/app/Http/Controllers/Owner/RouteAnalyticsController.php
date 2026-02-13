<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Services\RouteAnalyticsService;
use Illuminate\Http\Request;

class RouteAnalyticsController extends Controller
{
    protected $analyticsService;
    
    public function __construct(RouteAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }
    
    /**
     * Show analytics dashboard
     */
    public function index(Request $request)
    {
        $pageTitle = "Route Analytics";
        $owner = authUser();
        
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get top routes by profit
        $topRoutes = $this->analyticsService->getTopRoutes($owner->id, 5, 'profit', $startDate, $endDate);
        
        // Get all routes for dropdown
        $routes = Route::where('owner_id', $owner->id)->active()->get();
        
        return view('owner.analytics.index', compact('pageTitle', 'topRoutes', 'routes', 'startDate', 'endDate'));
    }
    
    /**
     * Show detailed route profitability
     */
    public function route(Request $request, $routeId)
    {
        $owner = authUser();
        $route = Route::findOrFail($routeId);
        
        // Authorization check
        if ($route->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->route('owner.analytics.index')->withNotify($notify);
        }
        
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $analytics = $this->analyticsService->getRouteProfitability($route, $startDate, $endDate);
        
        $pageTitle = "Analytics: {$route->title}";
        
        return view('owner.analytics.route', compact('pageTitle', 'route', 'analytics', 'startDate', 'endDate'));
    }
    
    /**
     * Compare routes (AJAX)
     */
    public function compare(Request $request)
    {
        $request->validate([
            'route_ids' => 'required|array|min:2|max:5',
            'route_ids.*' => 'exists:routes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $owner = authUser();
        
        // Verify all routes belong to owner
        $routes = Route::whereIn('id', $request->route_ids)
            ->where('owner_id', $owner->id)
            ->pluck('id');
        
        if ($routes->count() !== count($request->route_ids)) {
            return response()->json(['error' => 'Unauthorized access to some routes'], 403);
        }
        
        $comparison = $this->analyticsService->compareRoutes(
            $request->route_ids,
            $request->start_date,
            $request->end_date
        );
        
        return response()->json($comparison);
    }
    
    /**
     * Export analytics data (AJAX)
     */
    public function export(Request $request, $routeId)
    {
        $owner = authUser();
        $route = Route::findOrFail($routeId);
        
        // Authorization check
        if ($route->owner_id !== $owner->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $analytics = $this->analyticsService->getRouteProfitability($route, $startDate, $endDate);
        
        return response()->json($analytics);
    }
}
