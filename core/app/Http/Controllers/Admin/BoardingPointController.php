<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardingPoint;
use App\Models\City;
use App\Models\Counter;
use App\Models\Route;
use Illuminate\Http\Request;

class BoardingPointController extends Controller
{
    public function index()
    {
        $pageTitle = __('All Boarding Points');
        $boardingPoints = BoardingPoint::query()
            ->with(['owner', 'city', 'counter'])
            ->searchable(['name', 'landmark'])
            ->filter(['owner_id', 'city_id', 'type'])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.boarding-points.index', compact('pageTitle', 'boardingPoints'));
    }

    public function create()
    {
        $pageTitle = __('Create Boarding Point');
        $cities = City::active()->orderBy('name')->get();
        $counters = Counter::active()->orderBy('name')->get();
        return view('admin.boarding-points.create', compact('pageTitle', 'cities', 'counters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'nullable|integer|exists:owners,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'counter_id' => 'nullable|integer|exists:counters,id',
            'landmark' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'type' => 'required|in:bus_stand,highway_pickup,city_center,airport,custom',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $boardingPoint = new BoardingPoint();
        $boardingPoint->owner_id = $request->owner_id ?? 0; // 0 for global
        $boardingPoint->name = $request->name;
        $boardingPoint->city_id = $request->city_id;
        $boardingPoint->counter_id = $request->counter_id;
        $boardingPoint->landmark = $request->landmark;
        $boardingPoint->description = $request->description;
        $boardingPoint->address = $request->address;
        $boardingPoint->latitude = $request->latitude;
        $boardingPoint->longitude = $request->longitude;
        $boardingPoint->contact_phone = $request->contact_phone;
        $boardingPoint->contact_email = $request->contact_email;
        $boardingPoint->type = $request->type;
        $boardingPoint->is_active = $request->is_active ?? true;
        $boardingPoint->sort_order = $request->sort_order ?? 0;
        $boardingPoint->save();

        $notify[] = ['success', __('Boarding point created successfully')];
        return to_route('admin.boarding-points.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Boarding Point');
        $boardingPoint = BoardingPoint::findOrFail($id);
        $cities = City::active()->orderBy('name')->get();
        $counters = Counter::active()->orderBy('name')->get();
        return view('admin.boarding-points.edit', compact('pageTitle', 'boardingPoint', 'cities', 'counters'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'nullable|integer|exists:owners,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'counter_id' => 'nullable|integer|exists:counters,id',
            'landmark' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'type' => 'required|in:bus_stand,highway_pickup,city_center,airport,custom',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $boardingPoint = BoardingPoint::findOrFail($id);
        $boardingPoint->owner_id = $request->owner_id ?? 0;
        $boardingPoint->name = $request->name;
        $boardingPoint->city_id = $request->city_id;
        $boardingPoint->counter_id = $request->counter_id;
        $boardingPoint->landmark = $request->landmark;
        $boardingPoint->description = $request->description;
        $boardingPoint->address = $request->address;
        $boardingPoint->latitude = $request->latitude;
        $boardingPoint->longitude = $request->longitude;
        $boardingPoint->contact_phone = $request->contact_phone;
        $boardingPoint->contact_email = $request->contact_email;
        $boardingPoint->type = $request->type;
        $boardingPoint->is_active = $request->is_active ?? true;
        $boardingPoint->sort_order = $request->sort_order ?? 0;
        $boardingPoint->save();

        $notify[] = ['success', __('Boarding point updated successfully')];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $boardingPoint = BoardingPoint::findOrFail($id);
        $boardingPoint->is_active = !$boardingPoint->is_active;
        $boardingPoint->save();

        $notify[] = ['success', __('Boarding point status updated successfully')];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $boardingPoint = BoardingPoint::findOrFail($id);
        $boardingPoint->delete();

        $notify[] = ['success', __('Boarding point deleted successfully')];
        return back()->withNotify($notify);
    }

    public function assign($routeId)
    {
        $pageTitle = __('Assign Boarding Points to Route');
        $route = Route::with('boardingPoints')->findOrFail($routeId);
        $boardingPoints = BoardingPoint::active()
            ->when($route->owner_id, function ($query) use ($route) {
                return $query->where(function ($q) use ($route) {
                    $q->where('owner_id', $route->owner_id)
                      ->orWhere('owner_id', 0);
                });
            })
            ->orderBy('sort_order')
            ->get();

        return view('admin.boarding-points.assign', compact('pageTitle', 'route', 'boardingPoints'));
    }

    public function assignStore(Request $request, $routeId)
    {
        $request->validate([
            'boarding_point_ids' => 'required|array',
            'boarding_point_ids.*' => 'integer|exists:boarding_points,id',
            'pickup_time_offsets' => 'required|array',
            'pickup_time_offsets.*' => 'integer|min:0',
        ]);

        $route = Route::findOrFail($routeId);
        
        // Remove existing assignments
        $route->boardingPoints()->detach();

        // Add new assignments
        foreach ($request->boarding_point_ids as $index => $pointId) {
            $route->boardingPoints()->attach($pointId, [
                'pickup_time_offset' => $request->pickup_time_offsets[$index] ?? 0,
                'sort_order' => $index,
            ]);
        }

        $notify[] = ['success', __('Boarding points assigned successfully')];
        return to_route('admin.routes.show', $routeId)->withNotify($notify);
    }
}
