<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Counter;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $pageTitle = __('All Routes');
        $routes = Route::query()
            ->with(['owner', 'startingPoint', 'destinationPoint'])
            ->withCount('trips')
            ->searchable(['name'])
            ->filter(['owner_id'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.routes.index', compact('pageTitle', 'routes'));
    }

    public function show($id)
    {
        $route = Route::with(['owner', 'startingPoint', 'destinationPoint', 'trips', 'trips.owner', 'trips.schedule'])->findOrFail($id);
        $pageTitle = __('Route Detail') . ' - ' . $route->name;
        
        // Fetch counter details for stoppages if stored as IDs
        $stoppageIds = $route->stoppages ?? [];
        $stoppages = Counter::whereIn('id', $stoppageIds)->orderByRaw("FIELD(id, ".implode(',', $stoppageIds).")")->get();

        return view('admin.routes.show', compact('pageTitle', 'route', 'stoppages'));
    }

    public function create()
    {
        $pageTitle = __('Create New Route');
        $counters = Counter::active()->orderByDesc('id')->get();
        return view('admin.routes.create', compact('pageTitle', 'counters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'starting_point' => 'required|integer|gt:0|exists:counters,id',
            'destination_point' => 'required|integer|gt:0|exists:counters,id',
            'distance' => 'nullable|string|max:40',
            'time' => 'nullable|string|max:40',
            'stoppages' => 'nullable|array|min:1',
            'stoppages.*' => 'nullable|integer|gt:0|exists:counters,id'
        ], [
            'stoppages.*.numeric' => __('Invalid Stoppage Field')
        ]);

        if ($request->starting_point == $request->destination_point) {
            $notify[] = ['error', __('Starting point and destination point can\'t be the same.')];
            return back()->withNotify($notify);
        }

        $stoppages = $request->stoppages ? array_filter($request->stoppages) : [];

        if (!in_array($request->starting_point, $stoppages)) {
            array_unshift($stoppages, $request->starting_point);
        }

        if (!in_array($request->destination_point, $stoppages)) {
            array_push($stoppages, $request->destination_point);
        }

        $route = new Route();
        $route->owner_id = 0; // Admin-defined global route
        $route->name = $request->name;
        $route->starting_point = $request->starting_point;
        $route->destination_point = $request->destination_point;
        $route->stoppages = array_unique($stoppages);
        $route->distance = $request->distance;
        $route->time = $request->time;
        $route->save();

        $notify[] = ['success', __('Route created successfully')];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Route');
        $route = Route::findOrFail($id);
        $stoppagesData = $route->stoppages ?? [];

        // Extract only IDs from the stoppages array of objects
        // Format: [{"id": 1, "name": "Station A"}, {"id": 2, "name": "Station B"}]
        $stoppagesArray = collect($stoppagesData)->pluck('id')->filter()->all();

        // Remove starting and destination points
        $pos = array_search($route->starting_point, $stoppagesArray);
        if ($pos !== false) {
            unset($stoppagesArray[$pos]);
        }
        $pos = array_search($route->destination_point, $stoppagesArray);
        if ($pos !== false) {
            unset($stoppagesArray[$pos]);
        }

        // Re-index array to ensure sequential keys
        $stoppagesArray = array_values($stoppagesArray);

        if (!empty($stoppagesArray)) {
            // Fetch counters and sort them in PHP to maintain original order
            $counters = Counter::active()
                ->whereIn('id', $stoppagesArray)
                ->get();

            // Sort by the original stoppages array order
            $stoppages = collect($stoppagesArray)->map(function($id) use ($counters) {
                return $counters->firstWhere('id', $id);
            })->filter()->values();
        } else {
            $stoppages = [];
        }

        $counters = Counter::active()->orderByDesc('id')->get();
        return view('admin.routes.edit', compact('pageTitle', 'route', 'stoppages', 'counters'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'starting_point' => 'required|integer|gt:0|exists:counters,id',
            'destination_point' => 'required|integer|gt:0|exists:counters,id',
            'distance' => 'nullable|string|max:40',
            'time' => 'nullable|string|max:40',
            'stoppages' => 'nullable|array|min:1',
            'stoppages.*' => 'nullable|integer|gt:0|exists:counters,id'
        ], [
            'stoppages.*.numeric' => 'Invalid Stoppage Field'
        ]);

        if ($request->starting_point == $request->destination_point) {
            $notify[] = ['error', 'Starting point and destination point can\'t be the same.'];
            return back()->withNotify($notify);
        }

        $stoppages = $request->stoppages ? array_filter($request->stoppages) : [];

        if (!in_array($request->starting_point, $stoppages)) {
            array_unshift($stoppages, $request->starting_point);
        }

        if (!in_array($request->destination_point, $stoppages)) {
            array_push($stoppages, $request->destination_point);
        }

        $route = Route::findOrFail($id);
        $route->name = $request->name;
        $route->starting_point = $request->starting_point;
        $route->destination_point = $request->destination_point;
        $route->stoppages = array_unique($stoppages);
        $route->distance = $request->distance;
        $route->time = $request->time;
        $route->save();

        $notify[] = ['success', __('Route updated successfully')];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        $route = Route::findOrFail($id);
        $route->delete();

        $notify[] = ['success', __('Route deleted successfully')];
        return back()->withNotify($notify);
    }
}
