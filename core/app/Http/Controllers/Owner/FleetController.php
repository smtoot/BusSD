<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Models\SeatLayout;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FleetType;
use App\Models\Vehicle;

class FleetController extends Controller
{
    public function fleetType()
    {
        $pageTitle = 'Fleet Types';
        $fleetTypes = FleetType::searchable(['name'])
            ->with('seatLayout')
            ->where('owner_id', 0) // Show admin-defined global fleet types
            ->orderByDesc('id')
            ->paginate(getPaginate());
        $seatLayouts = SeatLayout::active()->where('owner_id', 0)->orderByDesc('id')->get();
        return view('owner.fleet_type.index', compact('pageTitle', 'fleetTypes', 'seatLayouts'));
    }

    public function fleetTypeStatus($id)
    {
        return FleetType::changeStatus($id);
    }

    public function seatLayout()
    {
        $pageTitle = 'Seat Layout Templates';
        $seatLayouts = SeatLayout::active()->where('owner_id', 0)->orderByDesc('id')->paginate(getPaginate());
        return view('owner.seat_layout.index', compact('pageTitle', 'seatLayouts'));
    }

    public function layoutStore(Request $request, $id = 0)
    {
        $notify[] = ['error', 'Manual layout creation is disabled. Please use Admin templates.'];
        return back()->withNotify($notify);
    }

    public function layoutStatus($id)
    {
        $notify[] = ['error', 'Status changes for templates are restricted.'];
        return back()->withNotify($notify);
    }

    public function vehicle()
    {
        $pageTitle  = 'All Vehicles';
        $owner      = authUser();
        $vehicles   = Vehicle::searchable(['nick_name', 'owner_phone', 'engine_no', 'chasis_no'])->where('owner_id', $owner->id)->orderByDesc('id')->paginate(getPaginate());
        $fleetTypes = FleetType::active()->where('owner_id', 0)->orderByDesc('id')->get(); // Show admin-defined fleet types
        return view('owner.vehicle.index', compact('pageTitle', 'vehicles', 'owner', 'fleetTypes'));
    }

    public function vehicleStore(Request $request, $id = 0)
    {
        $request->validate([
            'nick_name'       => 'required|string|max:255',
            'registration_no' => 'required|string|max:255|unique:vehicles,registration_no,' . $id,
            'engine_no'       => 'required|string|max:255|unique:vehicles,engine_no,' . $id,
            'model_no'        => 'required|string|max:255',
            'chasis_no'       => 'required|string|max:255|unique:vehicles,chasis_no,' . $id,
            'owner_name'      => 'required|string|max:255',
            'owner_phone'     => 'required|string|max:255',
            'brand_name'      => 'required|string|max:255',
            'fleet_type'      => 'required|integer|exists:fleet_types,id',
        ]);

        $owner = authUser();
        if ($id) {
            $vehicle = Vehicle::where('owner_id', $owner->id)->findOrFail($id);
            $message = 'Vehicle updated successfully';
        } else {
            $vehicle           = new Vehicle();
            $vehicle->owner_id = $owner->id;
            $message           = 'Vehicle created successfully';
        }

        $vehicle->fleet_type_id   = $request->fleet_type;
        $vehicle->nick_name       = $request->nick_name;
        $vehicle->registration_no = $request->registration_no;
        $vehicle->engine_no       = $request->engine_no;
        $vehicle->model_no        = $request->model_no;
        $vehicle->chasis_no       = $request->chasis_no;
        $vehicle->owner_name      = $request->owner_name;
        $vehicle->owner_phone     = $request->owner_phone;
        $vehicle->brand_name      = $request->brand_name;
        $vehicle->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function changeVehicleStatus($id)
    {
        return Vehicle::changeStatus($id);
    }
}
