<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Counter;
use App\Models\City;
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
        // Fetch counter details for stoppages if stored as IDs
        $stoppageData = $route->stoppages ?? [];
        $stoppageIds = [];
        
        foreach ($stoppageData as $item) {
            if (is_array($item) && isset($item['id'])) {
                $stoppageIds[] = $item['id'];
            } elseif (is_numeric($item)) {
                $stoppageIds[] = $item;
            }
        }
        
        if (!empty($stoppageIds)) {
            $stoppages = City::whereIn('id', $stoppageIds)->get();
            // Sort by the original order of IDs
            $stoppages = $stoppages->sortBy(function ($model) use ($stoppageIds) {
                return array_search($model->id, $stoppageIds);
            })->values();
        } else {
            $stoppages = collect([]);
        }

        return view('admin.routes.show', compact('pageTitle', 'route', 'stoppages'));
    }

    public function create()
    {
        $pageTitle = __('Create New Route');
        $cities = City::active()->orderBy('name')->get();
        return view('admin.routes.create', compact('pageTitle', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'starting_city_id' => 'required|integer|gt:0|exists:cities,id',
            'destination_city_id' => 'required|integer|gt:0|exists:cities,id',
            'distance' => 'nullable|string|max:40',
            'time' => 'nullable|string|max:40',
            'stoppages' => 'nullable|array|min:1',
            'stoppages.*' => 'nullable|integer|gt:0|exists:cities,id'
        ], [
            'stoppages.*.numeric' => __('Invalid Stoppage Field')
        ]);

        if ($request->starting_city_id == $request->destination_city_id) {
            $notify[] = ['error', __('Starting point and destination point can\'t be the same.')];
            return back()->withNotify($notify);
        }

        $stoppages = $request->stoppages ? array_filter($request->stoppages) : [];

        if (!in_array($request->starting_city_id, $stoppages)) {
            array_unshift($stoppages, $request->starting_city_id);
        }

        if (!in_array($request->destination_city_id, $stoppages)) {
            array_push($stoppages, $request->destination_city_id);
        }

        $route = new Route();
        $route->owner_id = 0; // Admin-defined global route
        $route->name = $request->name;
        $route->starting_city_id = $request->starting_city_id;
        $route->destination_city_id = $request->destination_city_id;
        $route->stoppages = array_unique($stoppages);
        $route->distance = $request->distance;
        $route->time = $request->time;
        $route->save();

        $notify[] = ['success', __('Route created successfully')];
        return to_route('admin.routes.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Route');
        $route = Route::findOrFail($id);
        $stoppagesData = $route->stoppages;
        
        // Standardize: stoppages are now simple ID arrays from the sortable list
        $stoppagesArray = [];
        if (is_array($stoppagesData)) {
            foreach ($stoppagesData as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $stoppagesArray[] = $item['id'];
                } elseif (is_numeric($item)) {
                    $stoppagesArray[] = $item;
                }
            }
        }

        if (!empty($stoppagesArray)) {
            $cityModels = City::active()->whereIn('id', $stoppagesArray)->get();
            // Sort by the order in the array
            $stoppages = collect($stoppagesArray)->map(function($id) use ($cityModels) {
                return $cityModels->firstWhere('id', $id);
            })->filter()->values();
        } else {
            $stoppages = collect([]);
        }

        $cities = City::active()->orderBy('name')->get();
        return view('admin.routes.edit', compact('pageTitle', 'route', 'stoppages', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'starting_city_id' => 'required|integer|gt:0|exists:cities,id',
            'destination_city_id' => 'required|integer|gt:0|exists:cities,id',
            'distance' => 'nullable|string|max:40',
            'time' => 'nullable|string|max:40',
            'stoppages' => 'nullable|array|min:1',
            'stoppages.*' => 'nullable|integer|gt:0|exists:cities,id'
        ], [
            'stoppages.*.numeric' => 'Invalid Stoppage Field'
        ]);

        if ($request->starting_city_id == $request->destination_city_id) {
            $notify[] = ['error', 'Starting point and destination point can\'t be the same.'];
            return back()->withNotify($notify);
        }

        $stoppages = $request->stoppages ? array_filter($request->stoppages) : [];

        if (!in_array($request->starting_city_id, $stoppages)) {
            array_unshift($stoppages, $request->starting_city_id);
        }

        if (!in_array($request->destination_city_id, $stoppages)) {
            array_push($stoppages, $request->destination_city_id);
        }

        $route = Route::findOrFail($id);
        $route->name = $request->name;
        $route->starting_city_id = $request->starting_city_id;
        $route->destination_city_id = $request->destination_city_id;
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
