<?php

namespace App\Http\Controllers\CoOwner;

use App\Http\Controllers\Controller;
use App\Models\AssignedBus;
use App\Models\Counter;
use App\Models\Driver;
use App\Models\FleetType;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Supervisor;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Trips';
        $owner = authUser('co-owner')->owner;
        $trips = Trip::where('owner_id', $owner->id)
            ->searchable(['title'])
            ->with(['fleetType', 'route', 'schedule'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->get();
        $schedules  = Schedule::active()->where('owner_id', $owner->id)->get();
        $routes     = Route::active()->where('owner_id', $owner->id)->get();

        return view('co_owner.trip.index', compact('pageTitle', 'trips', 'fleetTypes', 'schedules', 'routes'));
    }

    public function form($id = 0)
    {
        $owner = authUser('co-owner')->owner;
        if ($id) {
            $pageTitle = 'Edit Trip';
            $trip      = Trip::where('owner_id', $owner->id)->findOrFail($id);
        } else {
            $pageTitle = 'Create New Trip';
            $trip      = [];
        }
        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->get();
        $schedules  = Schedule::active()->where('owner_id', $owner->id)->get();
        $routes     = Route::active()->where('owner_id', $owner->id)->get();
        return view('co_owner.trip.form', compact('pageTitle', 'fleetTypes', 'schedules', 'routes', 'trip'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'title'      => 'required|string',
            'fleet_type' => 'required|integer|gt:0|exists:fleet_types,id',
            'route'      => 'required|integer|gt:0|exists:routes,id',
            'from'       => 'required|integer|gt:0',
            'to'         => 'required|integer|gt:0',
            'schedule'   => 'required|integer|gt:0|exists:schedules,id',
            'day_off'    => 'nullable|array|min:1',
            'day_off.*'  => 'nullable|integer|in:0,1,2,3,4,5,6'
        ]);

        $owner = authUser('co-owner')->owner;
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
        $trip->schedule_id       = $request->schedule;
        $trip->starting_point    = $request->from;
        $trip->destination_point = $request->to;
        $trip->day_off           = $request->day_off ?? [];
        $trip->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function changeTripStatus($id)
    {
        return Trip::changeStatus($id);
    }

    public function route()
    {
        $pageTitle = "All Routes";
        $owner     = authUser('co-owner')->owner;
        $routes    = Route::where('owner_id', $owner->id)->orderByDesc('id')->paginate(getPaginate());
        return view('co_owner.route.index', compact('pageTitle', 'routes'));
    }

    public function routeForm($id = 0)
    {
        $owner = authUser('co-owner')->owner;
        if ($id) {
            $pageTitle = 'Edit Route';
            $route = Route::where('owner_id', $owner->id)->with(['startingPoint', 'destinationPoint'])->findOrFail($id);
            $stoppagesArray = $route->stoppages;
            $pos = array_search($route->starting_point, $stoppagesArray);
            unset($stoppagesArray[$pos]);
            $pos = array_search($route->destination_point, $stoppagesArray);
            unset($stoppagesArray[$pos]);
            if (!empty($stoppagesArray)) {
                $stoppages = $owner->counters()
                    ->active()->whereIn('id', $stoppagesArray)
                    ->orderByRaw("field(id," . implode(',', $stoppagesArray) . ")")
                    ->get();
            } else {
                $stoppages = [];
            }
        } else {
            $pageTitle = 'Create New Route';
            $route = [];
            $stoppages = [];
        }
        $counters = Counter::active()->where('owner_id', $owner->id)->get();
        return view('co_owner.route.form', compact('pageTitle', 'counters', 'route', 'stoppages'));
    }

    public function routeStore(Request $request, $id = 0)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'starting_point'    => 'required|integer|gt:0|exists:counters,id',
            'destination_point' => 'required|integer|gt:0|exists:counters,id',
            'distance'          => 'nullable|string|max:40',
            'time'              => 'nullable|string|max:40',
            'stoppages'         => 'nullable|array|min:1',
            'stoppages.*'       => 'nullable|integer|gt:0|exists:counters,id'
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
        $owner = authUser('co-owner')->owner;
        if ($id) {
            $route   = Route::where('owner_id', $owner->id)->findOrFail($id);
            $message = 'Route updated successfully';
        } else {
            $route           = new Route();
            $route->owner_id = $owner->id;
            $message         = 'Route created successfully';
        }

        $route->name              = $request->name;
        $route->starting_point    = $request->starting_point;
        $route->destination_point = $request->destination_point;
        $route->stoppages         = array_unique($stoppages);
        $route->distance          = $request->distance;
        $route->time              = $request->time;
        $route->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function changeRouteStatus($id)
    {
        return Route::changeStatus($id);
    }

    public function assignVehicle()
    {
        $pageTitle = 'All Assigned Vehicles';
        $owner = authUser('co-owner')->owner;
        $trips = Trip::active()->where('owner_id', $owner->id)
            ->withActiveFleetType()
            ->WithActiveVehicle()
            ->get();

        $drivers     = Driver::active()->where('owner_id', $owner->id)->get();
        $supervisors = Supervisor::active()->where('owner_id', $owner->id)->get();

        $assignedVehicles = AssignedBus::searchable(['trip:title', 'vehicle:registration_no', 'driver:username', 'supervisor:username'])
            ->with(['vehicle', 'trip', 'supervisor', 'driver'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->get();
        $schedules  = Schedule::active()->where('owner_id', $owner->id)->get();
        $routes     = Route::active()->where('owner_id', $owner->id)->get();

        return view('co_owner.assign_vehicle.index', compact('pageTitle', 'trips', 'fleetTypes', 'schedules', 'routes', 'assignedVehicles', 'drivers', 'supervisors'));
    }

    public function assignVehicleStore(Request $request, $id = 0)
    {
        $request->validate([
            'trip'                    => 'required|integer|gt:0|exists:trips,id',
            'bus_registration_number' => 'required|integer|gt:0|exists:vehicles,id',
            'driver'                  => 'required|integer|gt:0|exists:drivers,id',
            'supervisor'              => 'required|integer|gt:0|exists:supervisors,id'
        ]);

        $owner = authUser('co-owner')->owner;

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
}
