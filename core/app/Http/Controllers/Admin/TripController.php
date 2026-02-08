<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Owner;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Trips';
        $trips = Trip::query()
            ->with(['owner', 'route', 'schedule', 'fleetType'])
            ->searchable(['title'])
            ->filter(['owner_id'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $owners = Owner::active()->orderBy('username')->get();
        return view('admin.trips.index', compact('pageTitle', 'trips', 'owners'));
    }

    public function show($id)
    {
        $trip = Trip::with(['owner', 'route', 'schedule', 'fleetType', 'vehicle', 'startingPoint', 'destinationPoint'])->findOrFail($id);
        $pageTitle = 'Trip Detail - ' . $trip->title;
        return view('admin.trips.show', compact('pageTitle', 'trip'));
    }

    public function status($id)
    {
        return Trip::changeStatus($id);
    }

    public function export()
    {
        $trips = Trip::with(['owner', 'route', 'schedule', 'fleetType'])->get();
        $filename = 'trips_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($trips) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Owner', 'Route', 'Status']);
            foreach ($trips as $trip) {
                fputcsv($file, [
                    $trip->id,
                    $trip->title,
                    @$trip->owner->username,
                    @$trip->route->name,
                    $trip->status ? 'Active' : 'Inactive'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
