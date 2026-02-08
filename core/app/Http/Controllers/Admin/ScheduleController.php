<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Schedules';
        $schedules = Schedule::query()
            ->with(['owner'])
            ->withCount('trips')
            ->searchable(['start_from', 'end_at'])
            ->filter(['owner_id', 'status'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.schedules.index', compact('pageTitle', 'schedules'));
    }
}
