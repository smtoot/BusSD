<?php

namespace App\Http\Controllers\CoOwner;

use Carbon\Carbon;
use App\Models\BookedTicket;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CoOwnerController extends Controller
{
    public function dashboard()
    {
        $pageTitle = "CoOwner Dashboard";
        $coOwner = authUser('co-owner');
        $owner = $coOwner->owner;

        $bookedTickets = $owner->routes()->with(['bookedTickets' => function ($bt) {
            $bt->selectRaw('booked_tickets.price * booked_tickets.ticket_count as total_amount')->where('booked_tickets.status', 1);
        }])->get();

        $monthlySales = $owner->bookedTickets()
            ->whereMonth('created_at', date('m'))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('F d');
            });

        $monthlySale['date']   = $monthlySales->keys();
        $monthlySale['amount'] = collect([]);

        $monthlySales->map(function ($ms) use ($monthlySale) {
            $monthlySale['amount']->push($ms->sum('price'));
        });

        $bookedTicket['route_name'] = collect([]);
        $bookedTicket['sale_price'] = collect([]);

        $bookedTickets->map(function ($bt) use ($bookedTicket) {
            $bookedTicket['route_name']->push($bt->name);
            $bookedTicket['sale_price']->push($bt->bookedTickets->sum('total_amount'));
        });

        $widget['total_bus']             = $owner->vehicles()->count();
        $widget['total_driver']          = $owner->drivers()->count();
        $widget['total_supervisor']      = $owner->supervisors()->count();
        $widget['total_coAdmin']         = $owner->coAdmins()->count();
        $widget['total_counter']         = $owner->counters()->count();
        $widget['total_counter_manager'] = $owner->counterManagers()->count();
        $widget['total_route']           = $owner->routes()->count();
        $widget['total_trip']            = $owner->trips()->count();
        $widget['active_packages']       = $owner->activePackages();

        return view('co_owner.dashboard', compact('bookedTicket', 'monthlySale', 'widget', 'pageTitle', 'owner'));
    }

    public function salesReport(Request $request)
    {
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $owner = authUser('co-owner')->owner;
        $bookedTickets = BookedTicket::where('owner_id', $owner->id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(price) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];
        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'bookedTickets' => getAmount($bookedTickets->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }
        $data = collect($data);
        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Sold',
                'data' => $data->pluck('bookedTickets')
            ]
        ];

        return response()->json($report);
    }

    private function getAllDates($startDate, $endDate)
    {
        $dates = [];
        $currentDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }
        return $dates;
    }

    private function  getAllMonths($startDate, $endDate)
    {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        $months = [];
        while ($startDate <= $endDate) {
            $months[] = $startDate->format('F-Y');
            $startDate->modify('+1 month');
        }
        return $months;
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $coOwner   = authUser('co-owner');
        return view('co_owner.profile', compact('pageTitle', 'coOwner'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:40',
            'firstname' => 'required|string|max:40',
            'address'   => 'nullable|string',
            'state'     => 'nullable|string|max:255',
            'zip'       => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:255',
            'image'     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $coOwner   = authUser('co-owner');
        if ($request->hasFile('image')) {
            try {
                $old = $coOwner->image;
                $coOwner->image = fileUploader($request->image, getFilePath('co_owner'), getFileSize('co_owner'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $coOwner->firstname = $request->firstname;
        $coOwner->lastname  = $request->lastname;
        $coOwner->address   = $request->address;
        $coOwner->state     = $request->state;
        $coOwner->zip       = $request->zip;
        $coOwner->city      = $request->city;
        $coOwner->save();

        $notify[] = ['success', 'Profile updated successfully.'];
        return to_route('co-owner.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $coOwner   = authUser('co-owner');
        return view('co_owner.password', compact('pageTitle', 'coOwner'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $coOwner = authUser('co-owner');
        if (!Hash::check($request->old_password, $coOwner->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $coOwner->password = Hash::make($request->password);
        $coOwner->save();

        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('co-owner.password')->withNotify($notify);
    }

    public function manageTransport()
    {
        $pageTitle = 'Manage Transports';
        $manageTransport = json_decode(file_get_contents(resource_path('views/co_owner/manage_transport.json')));
        return view('co_owner.manage_transport', compact('pageTitle', 'manageTransport'));
    }
}
