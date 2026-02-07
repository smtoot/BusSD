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

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'starts_from'   => 'required|date_format:H:i',
            'ends_at'       => 'required|date_format:H:i',
        ]);

        $owner = authUser();

        $startFrom = Carbon::parse($request->starts_from)->format('H:i:s');
        $endAt     = Carbon::parse($request->ends_at)->format('H:i:s');

        $schedule = Schedule::where('owner_id', $owner->id)->where('starts_from', $startFrom)->where('ends_at', $endAt)->first();
        if ($schedule) {
            $notify[] = ['error', 'This schedule has already been added'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $schedule = Schedule::find($id);
            $message  = 'Schedule updated successfully';
        } else {
            $schedule           = new Schedule();
            $schedule->owner_id = $owner->id;
            $message            = 'Schedule created successfully';
        }

        $schedule->starts_from = $request->starts_from;
        $schedule->ends_at     = $request->ends_at;
        $schedule->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function changeStatus($id)
    {
        return Schedule::changeStatus($id);
    }
}
