<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\RouteTemplate;
use App\Models\RouteTemplateStop;
use App\Models\Route;
use App\Models\City;
use Illuminate\Http\Request;

class RouteBuilderController extends Controller
{
    /**
     * Display list of route templates
     */
    public function index()
    {
        $pageTitle = "Route Templates";
        $owner = authUser();
        
        $templates = RouteTemplate::where('owner_id', $owner->id)
            ->with(['baseRoute:id,title', 'stops'])
            ->orderBy('created_at', 'desc')
            ->paginate(getPaginate());
        
        return view('owner.route_builder.index', compact('pageTitle', 'templates'));
    }
    
    /**
     * Show create form
     */
    public function create()
    {
        $pageTitle = "Create Route Template";
        $owner = authUser();
        
        $cities = City::active()->orderBy('name')->get();
        $routes = Route::where('owner_id', $owner->id)->active()->get();
        
        return view('owner.route_builder.create', compact('pageTitle', 'cities', 'routes'));
    }
    
    /**
     * Show edit form
     */
    public function edit($id)
    {
        $pageTitle = "Edit Route Template";
        $owner = authUser();
        
        $template = RouteTemplate::with('stops.city')->findOrFail($id);
        
        // Authorization check
        if ($template->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->route('owner.route.builder.index')->withNotify($notify);
        }
        
        $cities = City::active()->orderBy('name')->get();
        $routes = Route::where('owner_id', $owner->id)->active()->get();
        
        return view('owner.route_builder.edit', compact('pageTitle', 'template', 'cities', 'routes'));
    }
    
    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'base_route_id' => 'nullable|exists:routes,id',
            'stops' => 'required|array|min:2',
            'stops.*.city_id' => 'required|exists:cities,id',
            'stops.*.time_offset_minutes' => 'required|integer|min:0',
            'stops.*.dwell_time_minutes' => 'nullable|integer|min:1',
            'stops.*.distance_from_previous' => 'nullable|numeric|min:0',
        ]);
        
        $owner = authUser();
        
        $template = RouteTemplate::create([
            'owner_id' => $owner->id,
            'name' => $request->name,
            'description' => $request->description,
            'base_route_id' => $request->base_route_id,
            'is_active' => $request->has('is_active'),
        ]);
        
        // Create stops
        foreach ($request->stops as $index => $stopData) {
            RouteTemplateStop::create([
                'route_template_id' => $template->id,
                'city_id' => $stopData['city_id'],
                'sequence_order' => $index + 1,
                'time_offset_minutes' => $stopData['time_offset_minutes'],
                'dwell_time_minutes' => $stopData['dwell_time_minutes'] ?? 5,
                'distance_from_previous' => $stopData['distance_from_previous'] ?? 0,
                'boarding_allowed' => isset($stopData['boarding_allowed']) ? (bool) $stopData['boarding_allowed'] : true,
                'dropping_allowed' => isset($stopData['dropping_allowed']) ? (bool) $stopData['dropping_allowed'] : true,
                'notes' => $stopData['notes'] ?? null,
            ]);
        }
        
        // Recalculate totals
        $template->recalculateTotals();
        
        $notify[] = ['success', 'Route template created successfully'];
        return redirect()->route('owner.route.builder.index')->withNotify($notify);
    }
    
    /**
     * Update existing template
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'base_route_id' => 'nullable|exists:routes,id',
            'stops' => 'required|array|min:2',
            'stops.*.city_id' => 'required|exists:cities,id',
            'stops.*.time_offset_minutes' => 'required|integer|min:0',
            'stops.*.dwell_time_minutes' => 'nullable|integer|min:1',
            'stops.*.distance_from_previous' => 'nullable|numeric|min:0',
        ]);
        
        $owner = authUser();
        $template = RouteTemplate::findOrFail($id);
        
        // Authorization check
        if ($template->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->route('owner.route.builder.index')->withNotify($notify);
        }
        
        // Update template
        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'base_route_id' => $request->base_route_id,
            'is_active' => $request->has('is_active'),
        ]);
        
        // Delete old stops and create new ones
        $template->stops()->delete();
        
        foreach ($request->stops as $index => $stopData) {
            RouteTemplateStop::create([
                'route_template_id' => $template->id,
                'city_id' => $stopData['city_id'],
                'sequence_order' => $index + 1,
                'time_offset_minutes' => $stopData['time_offset_minutes'],
                'dwell_time_minutes' => $stopData['dwell_time_minutes'] ?? 5,
                'distance_from_previous' => $stopData['distance_from_previous'] ?? 0,
                'boarding_allowed' => isset($stopData['boarding_allowed']) ? (bool) $stopData['boarding_allowed'] : true,
                'dropping_allowed' => isset($stopData['dropping_allowed']) ? (bool) $stopData['dropping_allowed'] : true,
                'notes' => $stopData['notes'] ?? null,
            ]);
        }
        
        // Recalculate totals
        $template->recalculateTotals();
        
        $notify[] = ['success', 'Route template updated successfully'];
        return redirect()->route('owner.route.builder.index')->withNotify($notify);
    }
    
    /**
     * Toggle active status
     */
    public function status($id)
    {
        $owner = authUser();
        $template = RouteTemplate::findOrFail($id);
        
        // Authorization check
        if ($template->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->back()->withNotify($notify);
        }
        
        $template->is_active = !$template->is_active;
        $template->save();
        
        $status = $template->is_active ? 'activated' : 'deactivated';
        $notify[] = ['success', "Template {$status} successfully"];
        
        return redirect()->back()->withNotify($notify);
    }
    
    /**
     * Delete template
     */
    public function delete($id)
    {
        $owner = authUser();
        $template = RouteTemplate::findOrFail($id);
        
        // Authorization check
        if ($template->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->back()->withNotify($notify);
        }
        
        $template->delete();
        
        $notify[] = ['success', 'Template deleted successfully'];
        return redirect()->back()->withNotify($notify);
    }
    
    /**
     * Load template for AJAX (used in trip form)
     */
    public function load($id)
    {
        $owner = authUser();
        $template = RouteTemplate::with('stops.city')->findOrFail($id);
        
        // Authorization check
        if ($template->owner_id !== $owner->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'description' => $template->description,
            'total_duration' => $template->formatted_duration,
            'total_distance' => $template->total_distance_km,
            'stops' => $template->stops->map(function($stop) {
                return [
                    'id' => $stop->id,
                    'city_id' => $stop->city_id,
                    'city_name' => $stop->city->name,
                    'sequence_order' => $stop->sequence_order,
                    'time_offset_minutes' => $stop->time_offset_minutes,
                    'formatted_time_offset' => $stop->formatted_time_offset,
                    'dwell_time_minutes' => $stop->dwell_time_minutes,
                    'distance_from_previous' => $stop->distance_from_previous,
                    'boarding_allowed' => $stop->boarding_allowed,
                    'dropping_allowed' => $stop->dropping_allowed,
                    'notes' => $stop->notes,
                ];
            }),
        ]);
    }
}
