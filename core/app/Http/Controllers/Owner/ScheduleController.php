<?php

namespace App\Http\Controllers\Owner;

use Carbon\Carbon;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function index()
    {
        $pageTitle = "All Schedules";
        $owner = authUser();
        $schedules = Schedule::where('owner_id', $owner->id)->orderByDesc('id')->paginate(getPaginate());
        return view('owner.schedule.index', compact('pageTitle', 'schedules'));
    }

    public function create()
    {
        $pageTitle = "Create New Schedule";
        $owner = authUser();
        $routes = \App\Models\Route::where('owner_id', $owner->id)->get();
        $fleetTypes = \App\Models\FleetType::where('owner_id', $owner->id)->get();
        $vehicles = \App\Models\Vehicle::where('owner_id', $owner->id)->get();
        $availableAmenities = \App\Models\TripAmenity::getAvailableAmenities();
        return view('owner.schedule.form', compact('pageTitle', 'routes', 'fleetTypes', 'vehicles', 'availableAmenities'));
    }

    public function edit($id)
    {
        $pageTitle = "Edit Schedule";
        $owner = authUser();
        $schedule = Schedule::where('owner_id', $owner->id)->findOrFail($id);
        $routes = \App\Models\Route::where('owner_id', $owner->id)->get();
        $fleetTypes = \App\Models\FleetType::where('owner_id', $owner->id)->get();
        $vehicles = \App\Models\Vehicle::where('owner_id', $owner->id)->get();
        $availableAmenities = \App\Models\TripAmenity::getAvailableAmenities();
        return view('owner.schedule.form', compact('pageTitle', 'schedule', 'routes', 'fleetTypes', 'vehicles', 'availableAmenities'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'route_id'              => 'required|integer|exists:routes,id',
            'fleet_type_id'         => 'required|integer|exists:fleet_types,id',
            'starting_point'        => 'required|integer|exists:counters,id',
            'destination_point'     => 'required|integer|exists:counters,id',
            'starts_from'           => 'required|date_format:H:i',
            'ends_at'               => 'required|date_format:H:i',
            'duration_hours'        => 'required|integer|min:0',
            'duration_minutes'      => 'required|integer|min:0|max:59',
            'recurrence_type'       => 'required|in:daily,weekly',
            'recurrence_days'       => 'required_if:recurrence_type,weekly|array',
            'starts_on'             => 'required|date',
            'ends_on'               => 'nullable|date|after_or_equal:starts_on',
            'never_ends'            => 'nullable|string', // Checkbox
            'base_price'            => 'required|numeric|min:0',
            'inventory_allocation'  => 'required|in:all_seats,limited',
            'inventory_count'       => 'required_if:inventory_allocation,limited|nullable|integer|min:1',
            'cancellation_policy'   => 'required|string',
            'trip_type'             => 'required|in:express,semi_express,local,night',
            'trip_category'         => 'required|in:premium,standard,budget',
            'bus_type'              => 'nullable|string|max:100',
            'search_priority'       => 'required|integer|min:0|max:100',
            'trip_status'           => 'required|in:draft,pending,approved,active',
            'amenities'             => 'nullable|array',
        ]);

        $owner = authUser();

        if ($id) {
            $schedule = Schedule::where('owner_id', $owner->id)->findOrFail($id);
            $message  = 'Schedule updated successfully';
        } else {
            $schedule           = new Schedule();
            $schedule->owner_id = $owner->id;
            $message            = 'Schedule created successfully';
        }

        $schedule->name                 = $request->name;
        $schedule->route_id             = $request->route_id;
        $schedule->starting_point       = $request->starting_point;
        $schedule->destination_point    = $request->destination_point;
        $schedule->fleet_type_id        = $request->fleet_type_id;
        $schedule->vehicle_id           = $request->vehicle_id ?? 0;
        $schedule->starts_from          = $request->starts_from;
        $schedule->ends_at              = $request->ends_at;
        $schedule->duration_hours       = $request->duration_hours;
        $schedule->duration_minutes     = $request->duration_minutes;
        $schedule->recurrence_type      = $request->recurrence_type;
        $schedule->recurrence_days      = $request->recurrence_days;
        $schedule->starts_on            = $request->starts_on;
        $schedule->ends_on              = $request->ends_on;
        $schedule->never_ends           = $request->has('never_ends');
        $schedule->base_price           = $request->base_price;
        $schedule->inventory_allocation = $request->inventory_allocation;
        $schedule->inventory_count      = $request->inventory_count;
        $schedule->cancellation_policy  = $request->cancellation_policy;
        $schedule->trip_type            = $request->trip_type;
        $schedule->trip_category        = $request->trip_category;
        $schedule->bus_type             = $request->bus_type;
        $schedule->search_priority      = $request->search_priority;
        $schedule->trip_status          = $request->trip_status;
        $schedule->amenities            = $request->amenities;

        $schedule->save();

        // Trigger Trip Generation
        $generationService = new \App\Services\TripGenerationService();
        $generationService->generateForSchedule($schedule);

        $notify[] = ['success', $message];
        return to_route('owner.trip.schedule.index')->withNotify($notify);
    }

    public function changeStatus($id)
    {
        return Schedule::changeStatus($id);
    }
}
