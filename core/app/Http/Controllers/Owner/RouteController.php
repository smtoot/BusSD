<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Counter;
use App\Models\City;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $pageTitle = __('My Routes');
        $owner = authUser();
        $routes = Route::where('owner_id', $owner->id)
            ->with(['startingPoint', 'destinationPoint'])
            ->withCount('trips')
            ->searchable(['name'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.route.index', compact('pageTitle', 'routes'));
    }

    public function create()
    {
        $pageTitle = __('Create Route Variation');
        $owner = authUser();
        
        // Allowed city pairs are those defined by Admin in global routes
        $globalRoutes = Route::active()->where('owner_id', 0)->with(['startingPoint', 'destinationPoint'])->get();
        $cities = City::active()->orderBy('name')->get();
        
        return view('owner.route.form', compact('pageTitle', 'globalRoutes', 'cities'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'starting_city_id' => 'required|integer|exists:cities,id',
            'destination_city_id' => 'required|integer|exists:cities,id',
            'distance' => 'nullable|string|max:40',
            'time' => 'nullable|string|max:40',
            'stoppages' => 'nullable|array',
            'stoppages.*' => 'integer|exists:cities,id'
        ]);

        if ($request->starting_city_id == $request->destination_city_id) {
            $notify[] = ['error', __('Starting point and destination point can\'t be the same.')];
            return back()->withNotify($notify);
        }

        // Verify if this city pair is allowed by Admin
        $isAllowed = Route::active()->where('owner_id', 0)
            ->where('starting_city_id', $request->starting_city_id)
            ->where('destination_city_id', $request->destination_city_id)
            ->exists();

        if (!$isAllowed) {
            $notify[] = ['error', __('This city pair is not currently approved by Admin. Please contact Admin to allow this route corridor.')];
            return back()->withNotify($notify);
        }

        $owner = authUser();
        if ($id) {
            $route = Route::where('owner_id', $owner->id)->findOrFail($id);
            $message = __('Route updated successfully');
        } else {
            $route = new Route();
            $route->owner_id = $owner->id;
            $message = __('Route variation created successfully');
        }

        $stoppages = $request->stoppages ? array_filter($request->stoppages) : [];

        // Automation: Ensure start and end are in stoppages if not manually added
        if (!in_array($request->starting_city_id, $stoppages)) {
            array_unshift($stoppages, $request->starting_city_id);
        }

        if (!in_array($request->destination_city_id, $stoppages)) {
            array_push($stoppages, $request->destination_city_id);
        }

        $route->name = $request->name;
        $route->starting_city_id = $request->starting_city_id;
        $route->destination_city_id = $request->destination_city_id;
        $route->stoppages = array_unique($stoppages);
        $route->distance = $request->distance;
        $route->time = $request->time;
        $route->save();

        $notify[] = ['success', $message];
        return to_route('owner.route.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Route Variation');
        $owner = authUser();
        $route = Route::where('owner_id', $owner->id)->findOrFail($id);
        
        $globalRoutes = Route::active()->where('owner_id', 0)->with(['startingPoint', 'destinationPoint'])->get();
        $cities = City::active()->orderBy('name')->get();

        // Prepare stoppages for sortable UI
        $stoppageIds = $route->stoppages ?? [];
        $selectedStoppages = City::whereIn('id', $stoppageIds)->get();
        // Sort by the order in the array
        $selectedStoppages = collect($stoppageIds)->map(function($id) use ($selectedStoppages) {
            return $selectedStoppages->firstWhere('id', $id);
        })->filter()->values();

        return view('owner.route.form', compact('pageTitle', 'route', 'globalRoutes', 'cities', 'selectedStoppages'));
    }

    public function status($id)
    {
        return Route::where('owner_id', authUser()->id)->findOrFail($id)->changeStatus($id);
    }
}
