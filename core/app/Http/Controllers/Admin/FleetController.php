<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\FleetType;
use App\Models\SeatLayout;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function vehicles()
    {
        $pageTitle = 'All Vehicles';
        $vehicles = Vehicle::query()
            ->with(['owner', 'fleetType'])
            ->searchable(['nick_name', 'register_no'])
            ->filter(['owner_id', 'fleet_type_id', 'status'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.fleet.vehicles', compact('pageTitle', 'vehicles'));
    }

    public function vehicleShow($id)
    {
        $vehicle = Vehicle::with(['owner', 'fleetType', 'fleetType.seatLayout'])->findOrFail($id);
        $pageTitle = 'Vehicle Detail - ' . $vehicle->nick_name;
        return view('admin.fleet.vehicle_show', compact('pageTitle', $vehicle));
    }

    public function fleetTypes()
    {
        $pageTitle = 'All Fleet Types';
        $fleetTypes = FleetType::query()
            ->with(['owner', 'seatLayout'])
            ->searchable(['name'])
            ->filter(['owner_id', 'status'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.fleet.fleet_types', compact('pageTitle', 'fleetTypes'));
    }

    public function seatLayouts()
    {
        $pageTitle = 'All Seat Layouts';
        $seatLayouts = SeatLayout::query()
            ->with(['owner'])
            ->searchable(['name'])
            ->filter(['owner_id', 'status'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.fleet.seat_layouts', compact('pageTitle', 'seatLayouts'));
    }

    public function seatLayoutStore(Request $request, $id = 0)
    {
        $request->validate([
            'name'   => 'required|string|max:40',
            'schema' => 'required|json'
        ]);

        $schema = json_decode($request->schema, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $notify[] = ['error', 'Invalid JSON format'];
            return back()->withNotify($notify);
        }

        if (!isset($schema['meta']['grid']['rows']) || !isset($schema['layout'])) {
            $notify[] = ['error', 'Invalid schema structure'];
            return back()->withNotify($notify);
        }

        // Validate grid size limits
        if ($schema['meta']['grid']['rows'] > 50 || $schema['meta']['grid']['cols'] > 20) {
            $notify[] = ['error', 'Grid size exceeds limits (max 50 rows, 20 cols)'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $seatLayout = SeatLayout::findOrFail($id);
            $message    = 'Seat layout template updated successfully';
        } else {
            $seatLayout = new SeatLayout();
            $message    = 'Seat layout template created successfully';
        }

        $seatLayout->owner_id = 0; // Admin Managed
        $seatLayout->name     = $request->name;
        $seatLayout->schema   = $schema; // Store as array, Laravel will cast to JSON
        $seatLayout->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function seatLayoutStatus($id)
    {
        return SeatLayout::changeStatus($id);
    }

    public function export()
    {
        $vehicles = Vehicle::with(['owner', 'fleetType'])->get();
        $filename = 'vehicles_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($vehicles) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nick Name', 'Reg No', 'Owner', 'Fleet Type', 'Status']);
            foreach ($vehicles as $vehicle) {
                fputcsv($file, [
                    $vehicle->id,
                    $vehicle->nick_name,
                    $vehicle->register_no,
                    @$vehicle->owner->username,
                    @$vehicle->fleetType->name,
                    $vehicle->status ? 'Active' : 'Inactive'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Fleet Type CRUD Methods (Admin-defined global fleet types)
    public function createFleetType()
    {
        $pageTitle = 'Create Fleet Type';
        $seatLayouts = SeatLayout::active()->where('owner_id', 0)->orderByDesc('id')->get();
        return view('admin.fleet.create_fleet_type', compact('pageTitle', 'seatLayouts'));
    }

    public function storeFleetType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'seat_layout' => 'required|integer|gt:0|exists:seat_layouts,id',
            'deck' => 'required|integer|gt:0',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|integer|gt:0',
            'has_ac' => 'required|integer|in:' . \App\Constants\Status::YES . ',' . \App\Constants\Status::NO,
        ], [
            'seats.*.required' => 'Seat number for all deck is required',
            'seats.*.numeric' => 'Seat number for all deck is must be a number',
            'seats.*.gt:0' => 'Seat number for all deck is must be greater than 0',
        ]);

        $fleetType = new FleetType();
        $fleetType->owner_id = 0; // Admin-defined global fleet type
        $fleetType->seat_layout_id = $request->seat_layout;
        $fleetType->name = $request->name;
        $fleetType->deck = $request->deck;
        $fleetType->seats = $request->seats;
        $fleetType->has_ac = $request->has_ac;
        $fleetType->save();

        $notify[] = ['success', 'Fleet type created successfully'];
        return back()->withNotify($notify);
    }

    public function editFleetType($id)
    {
        $pageTitle = 'Edit Fleet Type';
        $fleetType = FleetType::findOrFail($id);
        $seatLayouts = SeatLayout::active()->where('owner_id', 0)->orderByDesc('id')->get();
        return view('admin.fleet.edit_fleet_type', compact('pageTitle', 'fleetType', 'seatLayouts'));
    }

    public function updateFleetType(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'seat_layout' => 'required|integer|gt:0|exists:seat_layouts,id',
            'deck' => 'required|integer|gt:0',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|integer|gt:0',
            'has_ac' => 'required|integer|in:' . \App\Constants\Status::YES . ',' . \App\Constants\Status::NO,
        ], [
            'seats.*.required' => 'Seat number for all deck is required',
            'seats.*.numeric' => 'Seat number for all deck is must be a number',
            'seats.*.gt:0' => 'Seat number for all deck is must be greater than 0',
        ]);

        $fleetType = FleetType::findOrFail($id);
        $fleetType->seat_layout_id = $request->seat_layout;
        $fleetType->name = $request->name;
        $fleetType->deck = $request->deck;
        $fleetType->seats = $request->seats;
        $fleetType->has_ac = $request->has_ac;
        $fleetType->save();

        $notify[] = ['success', 'Fleet type updated successfully'];
        return back()->withNotify($notify);
    }

    public function destroyFleetType($id)
    {
        $fleetType = FleetType::findOrFail($id);
        $fleetType->delete();

        $notify[] = ['success', 'Fleet type deleted successfully'];
        return back()->withNotify($notify);
    }
}
