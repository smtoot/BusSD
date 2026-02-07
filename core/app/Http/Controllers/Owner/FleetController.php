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
        $pageTitle   = 'Fleet Types';
        $owner       = authUser();
        $fleetTypes  = FleetType::searchable(['name'])->with('seatLayout')->where('owner_id', $owner->id)->orderByDesc('id')->paginate(getPaginate());
        $seatLayouts = SeatLayout::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
        return view('owner.fleet_type.index', compact('pageTitle', 'fleetTypes', 'seatLayouts'));
    }

    public function fleetTypeStore(Request $request, $id = 0)
    {
        $request->validate([
            'name'        => 'required|string|max:40',
            'seat_layout' => 'required|integer|gt:0|exists:seat_layouts,id',
            'deck'        => 'required|integer|gt:0',
            'seats'       => 'required|array|min:1',
            'seats.*'     => 'required|integer|gt:0',
            'has_ac'      => 'required|integer|in:' . Status::YES . ',' . Status::NO,
        ], [
            'seats.*.required' => 'Seat number for all deck is required',
            'seats.*.numeric'  => 'Seat number for all deck is must be a number',
            'seats.*.gt:0'     => 'Seat number for all deck is must be greater than 0',
        ]);

        $owner = authUser();
        if ($id) {
            $fleetType = FleetType::where('owner_id', $owner->id)->findOrFail($id);
            $message   = 'Fleet type updated successfully';
        } else {
            $fleetType           = new FleetType();
            $fleetType->owner_id = $owner->id;
            $message             = 'Fleet type created successfully';
        }

        $fleetType->seat_layout_id = $request->seat_layout;
        $fleetType->name           = $request->name;
        $fleetType->deck           = $request->deck;
        $fleetType->seats          = $request->seats;
        $fleetType->has_ac         = $request->has_ac;
        $fleetType->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function fleetTypeStatus($id)
    {
        return FleetType::changeStatus($id);
    }

    public function seatLayout()
    {
        $pageTitle = 'Seat Layouts';
        $owner = authUser();
        $seatLayouts = SeatLayout::searchable('layout')->where('owner_id', $owner->id)->orderByDesc('id')->paginate(getPaginate());

        return view('owner.seat_layout.index', compact('pageTitle', 'seatLayouts', 'owner'));
    }

    public function layoutStore(Request $request, $id = 0)
    {
        $request->validate([
            'layout' => 'required|string|max:40'
        ]);

        if ($id) {
            $seatLayout = SeatLayout::findOrFail($id);
            $message    = 'Seat layout updated successfully';
        } else {
            $seatLayout = new SeatLayout();
            $message    = 'Seat layout created successfully';
        }
        $seatLayout->owner_id = auth('owner')->id();
        $seatLayout->layout   = $request->layout;
        $seatLayout->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function layoutStatus($id)
    {
        return SeatLayout::changeStatus($id);
    }

    public function vehicle()
    {
        $pageTitle  = 'All Vehicles';
        $owner      = authUser();
        $vehicles   = Vehicle::searchable(['nick_name', 'owner_phone', 'engine_no', 'chasis_no'])->where('owner_id', $owner->id)->orderByDesc('id')->paginate(getPaginate());
        $fleetTypes = FleetType::active()->where('owner_id', $owner->id)->orderByDesc('id')->get();
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
