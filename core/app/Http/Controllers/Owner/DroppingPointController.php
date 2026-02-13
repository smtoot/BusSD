<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DroppingPoint;
use App\Models\Route;
use Illuminate\Http\Request;

class DroppingPointController extends Controller
{
    public function index()
    {
        $pageTitle = __('All Dropping Points');
        $droppingPoints = DroppingPoint::query()
            ->with(['city'])
            ->searchable(['name', 'landmark'])
            ->filter(['city_id', 'type'])
            ->where('owner_id', authUser()->id)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('owner.dropping-points.index', compact('pageTitle', 'droppingPoints'));
    }

    public function create()
    {
        $pageTitle = __('Create Dropping Point');
        $cities = City::active()->orderBy('name')->get();
        return view('owner.dropping-points.create', compact('pageTitle', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'nullable|integer|exists:cities,id',
            'landmark' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_phone' => 'nullable|string|max:20',
            'type' => 'required|in:bus_stand,city_center,airport,custom',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $droppingPoint = new DroppingPoint();
        $droppingPoint->owner_id = authUser()->id;
        $droppingPoint->name = $request->name;
        $droppingPoint->city_id = $request->city_id;
        $droppingPoint->landmark = $request->landmark;
        $droppingPoint->description = $request->description;
        $droppingPoint->address = $request->address;
        $droppingPoint->latitude = $request->latitude;
        $droppingPoint->longitude = $request->longitude;
        $droppingPoint->contact_phone = $request->contact_phone;
        $droppingPoint->type = $request->type;
        $droppingPoint->is_active = $request->is_active ?? true;
        $droppingPoint->sort_order = $request->sort_order ?? 0;
        $droppingPoint->save();

        $notify[] = ['success', __('Dropping point created successfully')];
        return to_route('owner.dropping-points.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Dropping Point');
        $droppingPoint = DroppingPoint::where('owner_id', authUser()->id)->findOrFail($id);
        $cities = City::active()->orderBy('name')->get();
        return view('owner.dropping-points.edit', compact('pageTitle', 'droppingPoint', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'nullable|integer|exists:cities,id',
            'landmark' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_phone' => 'nullable|string|max:20',
            'type' => 'required|in:bus_stand,city_center,airport,custom',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $droppingPoint = DroppingPoint::where('owner_id', authUser()->id)->findOrFail($id);
        $droppingPoint->name = $request->name;
        $droppingPoint->city_id = $request->city_id;
        $droppingPoint->landmark = $request->landmark;
        $droppingPoint->description = $request->description;
        $droppingPoint->address = $request->address;
        $droppingPoint->latitude = $request->latitude;
        $droppingPoint->longitude = $request->longitude;
        $droppingPoint->contact_phone = $request->contact_phone;
        $droppingPoint->type = $request->type;
        $droppingPoint->is_active = $request->is_active ?? true;
        $droppingPoint->sort_order = $request->sort_order ?? 0;
        $droppingPoint->save();

        $notify[] = ['success', __('Dropping point updated successfully')];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $droppingPoint = DroppingPoint::where('owner_id', authUser()->id)->findOrFail($id);
        $droppingPoint->is_active = !$droppingPoint->is_active;
        $droppingPoint->save();

        $notify[] = ['success', __('Dropping point status updated successfully')];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $droppingPoint = DroppingPoint::where('owner_id', authUser()->id)->findOrFail($id);
        $droppingPoint->delete();

        $notify[] = ['success', __('Dropping point deleted successfully')];
        return back()->withNotify($notify);
    }

    public function assign($routeId)
    {
        $pageTitle = __('Assign Dropping Points to Route');
        $route = Route::where('owner_id', authUser()->id)->with('droppingPoints')->findOrFail($routeId);
        $droppingPoints = DroppingPoint::active()
            ->where('owner_id', authUser()->id)
            ->orderBy('sort_order')
            ->get();

        return view('owner.dropping-points.assign', compact('pageTitle', 'route', 'droppingPoints'));
    }

    public function assignStore(Request $request, $routeId)
    {
        $request->validate([
            'dropping_point_ids' => 'required|array',
            'dropping_point_ids.*' => 'integer|exists:dropping_points,id',
            'dropoff_time_offsets' => 'required|array',
            'dropoff_time_offsets.*' => 'integer|min:0',
        ]);

        $route = Route::where('owner_id', authUser()->id)->findOrFail($routeId);
        
        // Remove existing assignments
        $route->droppingPoints()->detach();

        // Add new assignments
        foreach ($request->dropping_point_ids as $index => $pointId) {
            $route->droppingPoints()->attach($pointId, [
                'dropoff_time_offset' => $request->dropoff_time_offsets[$index] ?? 0,
                'sort_order' => $index,
            ]);
        }

        $notify[] = ['success', __('Dropping points assigned successfully')];
        return to_route('owner.route.show', $routeId)->withNotify($notify);
    }
}
