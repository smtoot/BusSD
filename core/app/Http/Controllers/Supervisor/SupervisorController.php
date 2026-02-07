<?php

namespace App\Http\Controllers\Supervisor;

use Carbon\Carbon;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        return to_route('supervisor.trips');
    }

    public function trips()
    {
        $supervisor = authUser('supervisor');
        $trips = $supervisor->assignedBuses()
            ->active()
            ->with('trip', 'supervisor', 'vehicle', 'vehicle.fleetType')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        $pageTitle = 'Assigned Trips';
        return view('supervisor.trips', compact('pageTitle', 'trips', 'supervisor'));
    }

    public function viewTrips($id)
    {
        $supervisor = authUser('supervisor');
        $trip = Trip::with(['route', 'fleetType', 'schedule', 'bookedTickets' => function ($q) {
            return $q->where('date_of_journey', Carbon::now()->format('Y-m-d'))->with('passenger');
        }])->findOrFail($id);

        $pageTitle = $trip->title;
        $stoppages = $trip->route->stoppages;
        return view('supervisor.trip_view', compact('pageTitle', 'trip', 'stoppages'));
    }


    public function profile()
    {
        $pageTitle = 'Profile';
        $supervisor = authUser('supervisor');
        return view('supervisor.profile', compact('pageTitle', 'supervisor'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'address'   => 'nullable|string',
            'state'     => 'nullable|string',
            'zip'       => 'nullable|string',
            'city'      => 'nullable|string',
            'image'     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        $supervisor = authUser('supervisor');
        if ($request->hasFile('image')) {
            try {
                $old = $supervisor->image;
                $supervisor->image = fileUploader($request->image, getFilePath('supervisor'), getFileSize('supervisor'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $supervisor->firstname = $request->firstname;
        $supervisor->lastname  = $request->lastname;
        $supervisor->address   = $request->address;
        $supervisor->state     = $request->state;
        $supervisor->zip       = $request->zip;
        $supervisor->city      = $request->city;
        $supervisor->save();

        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $supervisor = authUser('supervisor');
        return view('supervisor.password', compact('pageTitle', 'supervisor'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $supervisor = authUser('supervisor');
        if (!Hash::check($request->old_password, $supervisor->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $supervisor->password = Hash::make($request->password);
        $supervisor->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return back()->withNotify($notify);
    }
}
