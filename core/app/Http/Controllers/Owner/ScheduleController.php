<?php

namespace App\Http\Controllers\Owner;

use App\Models\TripAmenity;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\FleetType;
use App\Models\AmenityTemplate;
use App\Models\CancellationPolicy;
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
        // Allow Global Routes (owner_id = 0) + Owner's Routes
        $routes = Route::active()->with(['startingPoint', 'destinationPoint'])->where(function($q) use ($owner) {
            $q->where('owner_id', $owner->id)->orWhere('owner_id', 0);
        })->get();
        $fleetTypes = FleetType::where('owner_id', $owner->id)->get();
        $vehicles = Vehicle::where('owner_id', $owner->id)->with(['fleetType', 'amenities'])->get();
        
        // Get vehicle amenities (these will be inherited from selected vehicle)
        $vehicleAmenities = AmenityTemplate::where('amenity_type', 'vehicle')->active()->orderBy('sort_order')->get();
        
        // Get trip options (service offerings that can be configured per schedule)
        $tripAmenities = AmenityTemplate::where('amenity_type', 'trip')->active()->orderBy('sort_order')->get();
        
        // Get cancellation policies
        $policies = CancellationPolicy::active()->orderBy('sort_order')->get();
        
        // Phase 1.2: Load available boarding and dropping points
        $boardingPoints = \App\Models\BoardingPoint::where('owner_id', $owner->id)
            ->active()
            ->with('city')
            ->orderBy('sort_order')
            ->get();
            
        $droppingPoints = \App\Models\DroppingPoint::where('owner_id', $owner->id)
            ->active()
            ->with('city')
            ->orderBy('sort_order')
            ->get();

        return view('owner.schedule.form', compact('pageTitle', 'routes', 'fleetTypes', 'vehicles', 'vehicleAmenities', 'tripAmenities', 'policies', 'boardingPoints', 'droppingPoints'));
    }

    public function edit($id)
    {
        $pageTitle = "Edit Schedule";
        $owner = authUser();
        $schedule = Schedule::where('owner_id', $owner->id)->findOrFail($id);
        // Allow Global Routes (owner_id = 0) + Owner's Routes
        $routes = Route::active()->with(['startingPoint', 'destinationPoint'])->where(function($q) use ($owner) {
            $q->where('owner_id', $owner->id)->orWhere('owner_id', 0);
        })->get();
        $fleetTypes = FleetType::where('owner_id', $owner->id)->get();
        $vehicles = Vehicle::where('owner_id', $owner->id)->with(['fleetType', 'amenities'])->get();
        
        // Get vehicle amenities (these will be inherited from selected vehicle)
        $vehicleAmenities = AmenityTemplate::where('amenity_type', 'vehicle')->active()->orderBy('sort_order')->get();
        
        // Get trip options (service offerings that can be configured per schedule)
        $tripAmenities = AmenityTemplate::where('amenity_type', 'trip')->active()->orderBy('sort_order')->get();
        
        // Get cancellation policies
        $policies = CancellationPolicy::active()->orderBy('sort_order')->get();
        
        // Phase 1.2: Load available boarding and dropping points
        $boardingPoints = \App\Models\BoardingPoint::where('owner_id', $owner->id)
            ->active()
            ->with('city')
            ->orderBy('sort_order')
            ->get();
            
        $droppingPoints = \App\Models\DroppingPoint::where('owner_id', $owner->id)
            ->active()
            ->with('city')
            ->orderBy('sort_order')
            ->get();

        return view('owner.schedule.form', compact('pageTitle', 'schedule', 'routes', 'fleetTypes', 'vehicles', 'vehicleAmenities', 'tripAmenities', 'policies', 'boardingPoints', 'droppingPoints'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'route_id'              => ['required', 'integer', 'exists:routes,id', \Illuminate\Validation\Rule::exists('routes', 'id')->where(fn($q) => $q->where('owner_id', authUser()->id)->orWhere('owner_id', 0))],
            'fleet_type_id'         => ['required', 'integer', 'exists:fleet_types,id', \Illuminate\Validation\Rule::exists('fleet_types', 'id')->where(fn($q) => $q->where('owner_id', authUser()->id)->orWhere('owner_id', 0))],
            'starting_point'        => ['required', 'integer', \Illuminate\Validation\Rule::exists('branches', 'id')->where('owner_id', authUser()->id)],
            'destination_point'     => ['required', 'integer', \Illuminate\Validation\Rule::exists('branches', 'id')->where('owner_id', authUser()->id)],
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
            'cancellation_policy_id' => 'required|integer|exists:cancellation_policies,id',
            'trip_type'             => 'required|in:express,semi_express,local,night',
            'trip_category'         => 'required|in:premium,standard,budget',
            'bus_type'              => 'nullable|string|max:100',
            'search_priority'       => 'required|integer|min:0|max:100',
            'trip_status'           => 'required|in:draft,pending,approved,active',
            'amenities'             => 'nullable|array',
            'amenities.*'           => 'nullable|integer|exists:amenity_templates,id',
            // Phase 1.2: Boarding/Dropping Points
            'boarding_points'       => 'nullable|array',
            'boarding_points.*.point_id' => 'required|integer|exists:boarding_points,id',
            'boarding_points.*.offset_minutes' => 'required|integer|min:0',
            'boarding_points.*.notes' => 'nullable|string|max:500',
            'dropping_points'       => 'nullable|array',
            'dropping_points.*.point_id' => 'required|integer|exists:dropping_points,id',
            'dropping_points.*.offset_minutes' => 'required|integer|min:0',
            'dropping_points.*.notes' => 'nullable|string|max:500',
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
        $schedule->cancellation_policy_id = $request->cancellation_policy_id;
        $schedule->trip_type            = $request->trip_type;
        $schedule->trip_category        = $request->trip_category;
        $schedule->bus_type             = $request->bus_type;
        $schedule->search_priority      = $request->search_priority;
        $schedule->trip_status          = $request->trip_status;
        $schedule->amenities            = $request->amenities;

        $schedule->save();

        // Phase 1.2: Save boarding points
        if ($request->has('boarding_points') && is_array($request->boarding_points)) {
            // Delete existing points for updates
            $schedule->scheduleBoardingPoints()->delete();
            
            foreach ($request->boarding_points as $index => $pointData) {
                $schedule->scheduleBoardingPoints()->create([
                    'boarding_point_id' => $pointData['point_id'],
                    'time_offset_minutes' => $pointData['offset_minutes'],
                    'sort_order' => $index,
                    'notes' => $pointData['notes'] ?? null,
                ]);
            }
        }

        // Phase 1.2: Save dropping points
        if ($request->has('dropping_points') && is_array($request->dropping_points)) {
            // Delete existing points for updates
            $schedule->scheduleDroppingPoints()->delete();
            
            foreach ($request->dropping_points as $index => $pointData) {
                $schedule->scheduleDroppingPoints()->create([
                    'dropping_point_id' => $pointData['point_id'],
                    'time_offset_minutes' => $pointData['offset_minutes'],
                    'sort_order' => $index,
                    'notes' => $pointData['notes'] ?? null,
                ]);
            }
        }

        // Trigger Trip Generation
        $generationService = new \App\Services\TripGenerationService();
        $generationService->generateForSchedule($schedule);

        $notify[] = ['success', $message];
        return to_route('owner.trip.schedule.index')->withNotify($notify);
    }

    public function changeStatus($id)
    {
        return Schedule::where('owner_id', authUser()->id)->findOrFail($id)->changeStatus();
    }
}
