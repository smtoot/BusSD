<?php

namespace App\Http\Controllers\Driver;

use Carbon\Carbon;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    public function dashboard()
    {
        return to_route('driver.trips');
    }

    public function trips()
    {
        $driver = authUser('driver');
        $owner = $driver->owner;
        $trips = $driver->assignedBuses()
            ->active()
            ->with('trip', 'supervisor', 'vehicle', 'vehicle.fleetType')
            ->orderByDesc('id')
            ->paginate(getPaginate());
        $pageTitle = 'Assigned Trips';
        return view('driver.trips', compact('driver', 'owner', 'trips', 'pageTitle'));
    }

    public function viewTrips($id)
    {
        $trip = Trip::with(['route', 'fleetType', 'schedule', 'bookedTickets' => function ($q) {
            return $q->where('date_of_journey', Carbon::now()->format('Y-m-d'))->with('passenger');
        }])->findOrFail($id);

        $pageTitle = $trip->title;
        $stoppages = $trip->route->stoppages;
        return view('driver.trip_view', compact('pageTitle', 'trip', 'stoppages'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $driver = authUser('driver');
        return view('driver.profile', compact('pageTitle', 'driver'));
    }

    public function profileUpdate(Request $request)
    {
        $driver = authUser('driver');
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'address'   => 'nullable|string',
            'state'     => 'nullable|string',
            'zip'       => 'nullable|string',
            'city'      => 'nullable|string',
            'image'     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        if ($request->hasFile('image')) {
            try {
                $old = $driver->image;
                $driver->image = fileUploader($request->image, getFilePath('driver'), getFileSize('driver'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $driver->firstname = $request->firstname;
        $driver->lastname  = $request->lastname;
        $driver->address   = $request->address;
        $driver->state     = $request->state;
        $driver->zip       = $request->zip;
        $driver->city      = $request->city;
        $driver->save();

        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $driver = authUser('driver');
        return view('driver.password', compact('pageTitle', 'driver'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $driver = authUser('driver');
        if (!Hash::check($request->old_password, $driver->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $driver->password = Hash::make($request->password);
        $driver->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return back()->withNotify($notify);
    }
}
