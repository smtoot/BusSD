<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AssignedBus;
use App\Models\Counter;
use App\Models\Driver;
use App\Models\FleetType;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Supervisor;
use App\Models\TicketPrice;
use App\Models\Trip;
use App\Models\TripAmenity;
use App\Models\Vehicle;
use App\Models\CancellationPolicy;
use App\Models\AmenityTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Trips';
        $owner = authUser();
        $trips = Trip::where('owner_id', $owner->id)
            ->searchable(['title'])
            ->with(['fleetType', 'route', 'schedule'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $schedules  = Schedule::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $routes     = Route::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();

        return view('owner.trip.index', compact('pageTitle', 'trips', 'fleetTypes', 'schedules', 'routes'));
    }

    public function show($id)
    {
        $owner = authUser();
        $pageTitle = 'Trip Details';
        $trip = Trip::where('owner_id', $owner->id)->with(['fleetType', 'route', 'schedule', 'vehicle', 'amenities'])->findOrFail($id);
        return view('owner.trip.detail', compact('pageTitle', 'trip'));
    }

    public function form($id = 0)
    {
        $owner = authUser();
        if ($id) {
            $pageTitle = 'Edit Trip';
            $trip      = Trip::where('owner_id', $owner->id)->with('amenities')->findOrFail($id);
        } else {
            $pageTitle = 'Create New Trip';
            $trip      = null;
        }
        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $schedules  = Schedule::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $routes     = Route::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $vehicles   = Vehicle::active()->where('owner_id', $owner->id)->with(['fleetType', 'amenities'])->orderByDesc('id')->get();
        $drivers     = Driver::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $supervisors = Supervisor::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();

        // Get vehicle amenities (these will be inherited from selected vehicle)
        $vehicleAmenities = AmenityTemplate::where('amenity_type', 'vehicle')->active()->orderBy('sort_order')->get();
        
        // Get trip options (service offerings that can be configured per trip)
        $tripAmenities = AmenityTemplate::where('amenity_type', 'trip')->active()->orderBy('sort_order')->get();
        
        // Get cancellation policies
        $policies = CancellationPolicy::active()->orderBy('sort_order')->get();

        return view('owner.trip.form', compact('pageTitle', 'fleetTypes', 'schedules', 'routes', 'trip', 'vehicles', 'drivers', 'supervisors', 'vehicleAmenities', 'tripAmenities', 'policies'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'title'      => 'required|string',
            'fleet_type' => 'required|integer|gt:0|exists:fleet_types,id',
            'route'      => 'required|integer|gt:0|exists:routes,id',
            'from'       => 'required|integer|gt:0',
            'to'         => 'required|integer|gt:0',
            'schedule'   => 'nullable|integer|gt:0|exists:schedules,id', // Make schedule nullable/optional
            'departure_datetime' => 'required|date',
            'arrival_datetime'   => 'required|date|after:departure_datetime',
            'b2c_locked_seats' => 'nullable|array',
            'b2c_locked_seats.*' => 'nullable',
            // New redBus fields
            'trip_type'        => 'nullable|in:express,semi_express,local,night',
            'trip_category'    => 'nullable|in:premium,standard,budget',
            'bus_type'         => 'nullable|string|max:100',
            'base_price'       => 'nullable|numeric|gte:0',
            'inventory_allocation' => 'required|in:full,partial,reserved,custom',
            'inventory_count'  => 'nullable|integer|min:0',
            'cancellation_policy_id' => 'nullable|integer|exists:cancellation_policies,id',
            'weekend_surcharge' => 'nullable|numeric|gte:0|max:100',
            'holiday_surcharge' => 'nullable|numeric|gte:0|max:100',
            'early_bird_discount' => 'nullable|numeric|gte:0|max:100',
            'last_minute_surcharge' => 'nullable|numeric|gte:0|max:100',
            'search_priority'   => 'nullable|integer|min:0|max:100',
            'trip_status'      => 'nullable|in:draft,pending,approved,active',
            'amenities'        => 'nullable|array',
            'amenities.*'      => 'nullable|integer|exists:amenity_templates,id', // Validate against template IDs
            // Vehicle assignment
            'vehicle_id'       => 'required|integer|gt:0|exists:vehicles,id', // Made required
            'driver_id'        => 'nullable|integer|gt:0|exists:drivers,id',
            'supervisor_id'    => 'nullable|integer|gt:0|exists:supervisors,id',
        ], [
            'from.required' => trans('Invalid route selection. Please re-select the route.'),
            'to.required'   => trans('Invalid route selection. Please re-select the route.'),
            'from.gt'       => trans('Invalid route selection. Please re-select the route.'),
            'to.gt'         => trans('Invalid route selection. Please re-select the route.'),
        ]);

        $owner = authUser();
        if ($id) {
            $trip    = Trip::where('owner_id', $owner->id)->findOrFail($id);
            $message = 'Trip updated successfully';
        } else {
            $trip           = new Trip();
            $trip->owner_id = $owner->id;
            $message        = 'Trip created successfully';
        }
        $trip->title             = $request->title;
        $trip->fleet_type_id     = $request->fleet_type;
        $trip->route_id          = $request->route;
        
        // Handle inline schedule creation/reuse from DateTime
        if ($request->has('departure_datetime') && $request->has('arrival_datetime')) {
            $schedule = $this->createOrReuseScheduleFromDatetime($request, $owner);
            $trip->schedule_id = $schedule->id;
            
            // Set specific datetimes for this trip instance
            $trip->departure_datetime = $request->departure_datetime;
            $trip->arrival_datetime   = $request->arrival_datetime;
            $trip->date               = \Carbon\Carbon::parse($request->departure_datetime)->format('Y-m-d');
        } elseif ($request->filled('schedule')) {
            // Fallback: use schedule from hidden field (for editing existing trips)
            $trip->schedule_id = $request->schedule;
            // TODO: Calculate datetimes based on schedule if not provided? 
            // For now, wizard ensures datetimes are provided.
            $trip->departure_datetime = $request->departure_datetime;
            $trip->arrival_datetime   = $request->arrival_datetime;
            $trip->date               = \Carbon\Carbon::parse($request->departure_datetime)->format('Y-m-d');
        } else {
            $notify[] = ['error', trans('Please specify trip timing')];
            return back()->withNotify($notify)->withInput();
        }
        
        $trip->starting_point    = $request->from;
        $trip->destination_point = $request->to;
        // day_off removed
        $trip->b2c_locked_seats = $request->b2c_locked_seats ?? [];
        
        // Inventory & Policy
        $trip->inventory_allocation = $request->inventory_allocation;
        $trip->inventory_count      = $request->inventory_count;
        $trip->cancellation_policy_id = $request->cancellation_policy_id;
        $trip->seat_price           = $request->base_price; // Save base price to seat_price column as well for quick access

        // New redBus fields
        $trip->trip_type        = $request->trip_type ?? Trip::TRIP_TYPE_LOCAL;
        $trip->trip_category    = $request->trip_category ?? Trip::TRIP_CATEGORY_STANDARD;
        $trip->bus_type         = $request->bus_type;
        $trip->base_price       = $request->base_price;
        $trip->weekend_surcharge = $request->weekend_surcharge ?? 0;
        $trip->holiday_surcharge = $request->holiday_surcharge ?? 0;
        $trip->early_bird_discount = $request->early_bird_discount ?? 0;
        $trip->last_minute_surcharge = $request->last_minute_surcharge ?? 0;
        $trip->search_priority   = $request->search_priority ?? 50;
        $trip->trip_status      = $request->trip_status ?? Trip::TRIP_STATUS_DRAFT;

        // Handle inline ticket pricing if provided
        if ($request->has('main_price') && $request->has('price')) {
            $this->createOrUpdateTicketPrice($request, $owner);
        } elseif ($request->filled('base_price')) {
            // Wizard Step 2 logic: Handle simplified pricing
            $ticketPrice = TicketPrice::where('owner_id', $owner->id)
                ->where('route_id', $request->route)
                ->where('fleet_type_id', $request->fleet_type)
                ->first();

            if (!$ticketPrice) {
                $ticketPrice = new TicketPrice();
                $ticketPrice->owner_id = $owner->id;
                $ticketPrice->route_id = $request->route;
                $ticketPrice->fleet_type_id = $request->fleet_type;
            }
            $ticketPrice->main_price = $request->base_price;
            $ticketPrice->save();
        } else {
            // Verify ticket price exists if inline pricing is not provided
            $ticketPrice = TicketPrice::where('owner_id', $owner->id)
                ->where('route_id', $request->route)
                ->where('fleet_type_id', $request->fleet_type)
                ->first();
                
            if (!$ticketPrice) {
                $notify[] = ['error', trans('Ticket price not added for this fleet-route combination yet. Please add ticket price before creating a trip.')];
                return back()->withNotify($notify)->withInput();
            }
        }

        $trip->save();

        // Save amenities (JSON)
        if ($request->has('amenities')) {
            $trip->amenities = $request->amenities; // Save as JSON
            $trip->save();
            
            // Sync with legacy table for backward compatibility if needed, 
            // but for now we rely on the JSON column as per new design.
            // If API uses TripAmenity table, we might need to sync.
            // Let's sync just in case.
            TripAmenity::where('trip_id', $trip->id)->delete();
            foreach ($request->amenities as $amenityId) {
                // Fetch amenity name from template to store in legacy table if it expects string
                // Or if it expects just a string representation.
                // The legacy table 'trip_amenities' has 'amenity' column which is string.
                $template = AmenityTemplate::find($amenityId);
                if($template) {
                     TripAmenity::create([
                        'trip_id' => $trip->id,
                        'amenity' => $template->key ?? $template->label,
                    ]);
                }
            }
        }

        // Assign vehicle if provided - save trip first to establish relationships
        if ($request->filled('vehicle_id') && $request->filled('driver_id') && $request->filled('supervisor_id')) {
            // Save trip first to establish schedule relationship
            $trip->save();
            $this->assignVehicleToTrip($trip, $request->vehicle_id, $request->driver_id, $request->supervisor_id);
        }

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    /**
     * Assign vehicle, driver, and supervisor to trip
     */
    private function assignVehicleToTrip($trip, $vehicleId, $driverId, $supervisorId)
    {
        $owner = authUser();

        // Check if vehicle belongs to owner
        $vehicle = Vehicle::where('owner_id', $owner->id)->findOrFail($vehicleId);

        // Check if driver belongs to owner
        $driver = Driver::where('owner_id', $owner->id)->findOrFail($driverId);

        // Check if supervisor belongs to owner
        $supervisor = Supervisor::where('owner_id', $owner->id)->findOrFail($supervisorId);

        // Check if trip already has an assigned vehicle
        $existingAssignment = AssignedBus::where('trip_id', $trip->id)->first();

        if ($existingAssignment) {
            // Update existing assignment
            $existingAssignment->vehicle_id = $vehicleId;
            $existingAssignment->driver_id = $driverId;
            $existingAssignment->supervisor_id = $supervisorId;
            $existingAssignment->starts_from = $trip->schedule->starts_from;
            $existingAssignment->ends_at = $trip->schedule->ends_at;
            $existingAssignment->save();
        } else {
            // Create new assignment
            $assignedVehicle = new AssignedBus();
            $assignedVehicle->owner_id = $owner->id;
            $assignedVehicle->trip_id = $trip->id;
            $assignedVehicle->vehicle_id = $vehicleId;
            $assignedVehicle->driver_id = $driverId;
            $assignedVehicle->supervisor_id = $supervisorId;
            $assignedVehicle->starts_from = $trip->schedule->starts_from;
            $assignedVehicle->ends_at = $trip->schedule->ends_at;
            $assignedVehicle->save();
        }
    }

    /**
     * Get pricing preview for trip
     */
    public function getPricingPreview(Request $request)
    {
        $request->validate([
            'fleet_type_id' => 'required|integer|exists:fleet_types,id',
            'route_id' => 'required|integer|exists:routes,id',
            'base_price' => 'nullable|numeric|gte:0',
            'weekend_surcharge' => 'nullable|numeric|gte:0|max:100',
            'holiday_surcharge' => 'nullable|numeric|gte:0|max:100',
            'early_bird_discount' => 'nullable|numeric|gte:0|max:100',
            'last_minute_surcharge' => 'nullable|numeric|gte:0|max:100',
        ]);

        $owner = authUser();
        $commissionRate = $owner->commission_rate ?? gs('b2c_commission', 10);

        // Get base price from request or TicketPrice table
        $basePrice = $request->base_price;
        if (!$basePrice) {
            $ticketPrice = TicketPrice::where('owner_id', $owner->id)
                ->where('route_id', $request->route_id)
                ->where('fleet_type_id', $request->fleet_type_id)
                ->where('status', 1)
                ->first();
            $basePrice = $ticketPrice ? $ticketPrice->main_price : 0;
        }

        // Calculate adjusted price
        $price = $basePrice;
        $weekendSurcharge = $request->weekend_surcharge ?? 0;
        $holidaySurcharge = $request->holiday_surcharge ?? 0;
        $earlyBirdDiscount = $request->early_bird_discount ?? 0;
        $lastMinuteSurcharge = $request->last_minute_surcharge ?? 0;

        // Apply surcharges
        if ($weekendSurcharge > 0) {
            $price += $price * ($weekendSurcharge / 100);
        }
        if ($holidaySurcharge > 0) {
            $price += $price * ($holidaySurcharge / 100);
        }
        if ($lastMinuteSurcharge > 0) {
            $price += $price * ($lastMinuteSurcharge / 100);
        }
        // Apply discount
        if ($earlyBirdDiscount > 0) {
            $price -= $price * ($earlyBirdDiscount / 100);
        }

        $price = max(0, round($price, 2));
        $commission = round($price * ($commissionRate / 100), 2);
        $netRevenue = $price - $commission;

        // Get seat count from fleet type
        $fleetType = FleetType::find($request->fleet_type_id);
        $seatCount = $fleetType ? $fleetType->seats->total ?? 0 : 0;

        $expectedGross = $price * $seatCount;
        $expectedCommission = $commission * $seatCount;
        $expectedNet = $netRevenue * $seatCount;

        return response()->json([
            'status' => 'success',
            'data' => [
                'base_price' => $basePrice,
                'final_price' => $price,
                'commission_rate' => $commissionRate,
                'commission_per_booking' => $commission,
                'net_revenue_per_booking' => $netRevenue,
                'seat_count' => $seatCount,
                'expected_gross_revenue' => $expectedGross,
                'expected_commission' => $expectedCommission,
                'expected_net_revenue' => $expectedNet,
                'profitability' => $commissionRate < 20 ? 'Good' : ($commissionRate < 30 ? 'Fair' : 'Low'),
            ]
        ]);
    }

    /**
     * Get available vehicles for trip
     */
    public function getAvailableVehicles(Request $request)
    {
        $request->validate([
            'fleet_type_id' => 'required|integer|exists:fleet_types,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
        ]);

        $owner = authUser();

        // Get schedule times
        $schedule = Schedule::findOrFail($request->schedule_id);
        $startTime = Carbon::parse($schedule->starts_from)->format('H:i:s');
        $endTime = Carbon::parse($schedule->ends_at)->format('H:i:s');

        // Get vehicles for this fleet type
        $vehicles = Vehicle::where('owner_id', $owner->id)
            ->where('fleet_type_id', $request->fleet_type_id)
            ->where('status', 1)
            ->with(['fleetType'])
            ->get();

        // Check availability for each vehicle
        $availableVehicles = $vehicles->map(function ($vehicle) use ($owner, $startTime, $endTime) {
            $conflict = AssignedBus::where('owner_id', $owner->id)
                ->where('vehicle_id', $vehicle->id)
                ->where('status', 1)
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('starts_from', '<', $endTime)
                        ->where('ends_at', '>', $startTime);
                })
                ->first();

            return [
                'id' => $vehicle->id,
                'registration_no' => $vehicle->registration_no,
                'nick_name' => $vehicle->nick_name,
                'brand_name' => $vehicle->brand_name,
                'available' => !$conflict,
                'conflict_reason' => $conflict ? 'Already assigned to another trip' : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $availableVehicles,
        ]);
    }

    public function changeTripStatus($id)
    {
        return Trip::changeStatus($id);
    }

    public function route()
    {
        $pageTitle = "All Routes";
        $routes = Route::where('owner_id', 0) // Show admin-defined global routes
            ->orderByDesc('id')
            ->paginate(getPaginate());
        return view('owner.route.index', compact('pageTitle', 'routes'));
    }

    public function changeRouteStatus($id)
    {
        return Route::changeStatus($id);
    }

    public function assignVehicle()
    {
        $pageTitle = 'All Assigned Vehicles';
        $owner = authUser();
        $trips = Trip::active()->where('owner_id', $owner->id)
            ->withActiveFleetType()
            ->WithActiveVehicle()
            ->orderByDesc('id')
            ->get();

        $drivers     = Driver::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $supervisors = Supervisor::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();

        $assignedVehicles = AssignedBus::searchable(['trip:title', 'vehicle:registration_no', 'driver:username', 'supervisor:username'])
            ->with(['vehicle', 'trip', 'supervisor', 'driver'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $schedules  = Schedule::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        $routes     = Route::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();

        return view('owner.assign_vehicle.index', compact('pageTitle', 'trips', 'fleetTypes', 'schedules', 'routes', 'assignedVehicles', 'drivers', 'supervisors'));
    }

    public function assignVehicleStore(Request $request, $id = 0)
    {
        $request->validate([
            'trip'                    => 'required|integer|gt:0|exists:trips,id',
            'bus_registration_number' => 'required|integer|gt:0|exists:vehicles,id',
            'driver'                  => 'required|integer|gt:0|exists:drivers,id',
            'supervisor'              => 'required|integer|gt:0|exists:supervisors,id'
        ]);

        $owner = authUser();

        $checkTrip = AssignedBus::where('owner_id', $owner->id)->where('trip_id', $request->trip)->where('id', '!=', $id)->first();
        if ($checkTrip) {
            $notify[] = ['error', 'A vehicle had already been assigned to this trip'];
            return back()->withNotify($notify);
        }
        $trip      = Trip::with('schedule')->findOrFail($request->trip);
        $startTime = Carbon::parse($trip->schedule->starts_time)->format('H:i:s');
        $endTime   = Carbon::parse($trip->schedule->ends_at)->format('H:i:s');

        $checkVehicle = AssignedBus::where('owner_id', $owner->id)
            ->where(function ($q) use ($startTime, $endTime, $id, $request) {
                $q->where('starts_from', '>', $startTime)
                    ->where('starts_from', '<', $endTime)
                    ->where('id', '!=', $id)
                    ->where('vehicle_id', $request->bus_registration_number);
            })->orWhere(function ($q) use ($startTime, $endTime, $id, $request) {
                $q->where('ends_at', '>', $startTime)
                    ->where('ends_at', '<', $endTime)
                    ->where('id', '!=', $id)
                    ->where('vehicle_id', $request->bus_registration_number);
            })->first();
        if ($checkVehicle) {
            $notify[] = ['error', 'This vehicle had already been assigned to another trip on this time'];
            return back()->withNotify($notify);
        }

        $checkDriver = AssignedBus::where('owner_id', $owner->id)
            ->where(function ($q) use ($startTime, $endTime, $id, $request) {
                $q->where('starts_from', '>', $startTime)
                    ->where('starts_from', '<', $endTime)
                    ->where('driver_id', $request->driver)
                    ->where('id', '!=', $id);
            })->orWhere(function ($q) use ($startTime, $endTime, $id, $request) {
                $q->where('ends_at', '>', $startTime)
                    ->where('ends_at', '<', $endTime)
                    ->where('driver_id', $request->driver)
                    ->where('id', '!=', $id);
            })->first();
        if ($checkDriver) {
            $notify[] = ['error', 'This driver had already been assigned to another bus on this time'];
            return back()->withNotify($notify);
        }

        $checkSupervisor = AssignedBus::where('owner_id', $owner->id)
            ->where(function ($q) use ($startTime, $endTime, $id, $request) {
                $q->where('starts_from', '>', $startTime)
                    ->where('starts_from', '<', $endTime)
                    ->where('id', '!=', $id)
                    ->where('supervisor_id', $request->supervisor);
            })->orWhere(function ($q) use ($startTime, $endTime, $id, $request) {
                $q->where('ends_at', '>', $startTime)
                    ->where('ends_at', '<', $endTime)
                    ->where('id', '!=', $id)
                    ->where('supervisor_id', $request->supervisor);
            })->first();
        if ($checkSupervisor) {
            $notify[] = ['error', 'This supervisor had already been assigned to another bus on this time'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $assignedVehicle = AssignedBus::where('owner_id', $owner->id)->findOrFail($id);
            $message         = 'Data updated successfully';
        } else {
            $assignedVehicle           = new AssignedBus();
            $assignedVehicle->owner_id = $owner->id;
            $message                   = 'Vehicle assigned successfully';
        }
        $assignedVehicle->trip_id       = $request->trip;
        $assignedVehicle->vehicle_id    = $request->bus_registration_number;
        $assignedVehicle->driver_id     = $request->driver;
        $assignedVehicle->supervisor_id = $request->supervisor;
        $assignedVehicle->starts_from   = $trip->schedule->starts_from;
        $assignedVehicle->ends_at       = $trip->schedule->ends_at;
        $assignedVehicle->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function changeAssignVehicleStatus($id)
    {
        return AssignedBus::changeStatus($id);
    }

    /**
     * Create or update ticket pricing from inline pricing form
     */
    private function createOrUpdateTicketPrice(Request $request, $owner)
    {
        // Check if pricing already exists
        $ticketPrice = TicketPrice::where('owner_id', $owner->id)
            ->where('route_id', $request->route)
            ->where('fleet_type_id', $request->fleet_type)
            ->first();

        if ($ticketPrice) {
            // Pricing already exists, skip creation (should not happen in normal flow)
            return;
        }

        // Validate inline pricing data
        $request->validate([
            'main_price' => 'required|numeric|gt:0',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|gte:0',
        ], [
            'main_price.required' => trans('Main price is required'),
            'main_price.gt' => trans('Main price must be greater than zero'),
            'price.required' => trans('All stoppage prices are required'),
            'price.*.required' => trans('All price fields are required'),
            'price.*.numeric' => trans('All price fields should be numbers'),
        ]);

        // Create ticket price
        $ticketPrice = new TicketPrice();
        $ticketPrice->owner_id = $owner->id;
        $ticketPrice->route_id = $request->route;
        $ticketPrice->fleet_type_id = $request->fleet_type;
        $ticketPrice->main_price = $request->main_price;
        $ticketPrice->save();

        // Create stoppage prices
        foreach ($request->price as $key => $price) {
            $idArray = explode('-', $key);
            \App\Models\TicketPriceByStoppage::create([
                'ticket_price_id' => $ticketPrice->id,
                'source_destination' => $idArray,
                'price' => $price,
            ]);
        }
    }

    /**
     * Create or reuse schedule from inline timing data
     */
    /**
     * Create or reuse schedule from inline timing data (Datetimes)
     */
    private function createOrReuseScheduleFromDatetime(Request $request, $owner)
    {
        $departureDatetime = Carbon::parse($request->departure_datetime);
        $arrivalDatetime   = Carbon::parse($request->arrival_datetime);
        
        $departureTime = $departureDatetime->format('H:i:s');
        $arrivalTime   = $arrivalDatetime->format('H:i:s');
        $routeId       = $request->route;

        // Check if a similar schedule already exists (to promote reusability)
        $existingSchedule = Schedule::where('owner_id', $owner->id)
            ->where('route_id', $routeId)
            ->whereTime('starts_from', '=', $departureTime)
            ->whereTime('ends_at', '=', $arrivalTime)
            ->first();

        if ($existingSchedule) {
            return $existingSchedule; // Reuse existing schedule
        }

        // Create new schedule template
        $route = Route::findOrFail($routeId);
        
        $schedule = new Schedule();
        $schedule->owner_id = $owner->id;
        $schedule->route_id = $routeId;
        
        // Auto-generate schedule title
        $timeStr = $departureDatetime->format('h:i A');
        $schedule->name = "{$route->name} - {$timeStr}";
        
        // Set timing (using today's date as base for the template)
        $baseDate = Carbon::today();
        $schedule->starts_from = $baseDate->copy()->setTimeFromTimeString($departureTime);
        $schedule->ends_at     = $baseDate->copy()->setTimeFromTimeString($arrivalTime);
        
        if ($arrivalDatetime->lessThan($departureDatetime)) {
             // If arrival is next day in the specific instance, 
             // we should check if the time part implies overnight.
             // e.g. Dep 23:00, Arr 02:00. 
             // Intrinsic checks:
             if ($schedule->ends_at->lessThanOrEqualTo($schedule->starts_from)) {
                  $schedule->ends_at->addDay();
             }
        }
        
        $schedule->save();
        return $schedule;
    }
}
