<?php

namespace App\Http\Controllers\Owner;

use Carbon\Carbon;
use App\Models\Feature;
use App\Models\Package;
use App\Constants\Status;
use App\Models\SoldPackage;
use App\Models\BookedTicket;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $pageTitle  = 'Owner Dashboard';
        $owner = authUser('owner');

        $routes = $owner->routes();
        $bookedTickets = $owner->routes()->with(['bookedTickets' => function ($bt) {
            $bt->selectRaw('booked_tickets.price * booked_tickets.ticket_count as total_amount')->where('booked_tickets.status', 1);
        }])->get();

        $monthlySale = $owner->bookedTickets()
            ->whereMonth('created_at', date('m'))
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('F d');
            });

        $monthlySale['date']   = $monthlySale->keys();
        $monthlySale['amount'] = collect([]);

        $monthlySale->map(function ($ms) use ($monthlySale) {
            $monthlySale['amount']->push($ms->sum('price'));
        });
        // Top Routes by Revenue (for table display)
        $topRoutes = collect();
        $totalRouteRevenue = 0;

        $bookedTickets->each(function ($route) use ($topRoutes, &$totalRouteRevenue) {
            $revenue = $route->bookedTickets->sum('total_amount');
            $bookingCount = $route->bookedTickets->count();
            $totalRouteRevenue += $revenue;
            $topRoutes->push([
                'name'          => $route->name,
                'revenue'       => $revenue,
                'booking_count' => $bookingCount,
            ]);
        });

        $topRoutes = $topRoutes->sortByDesc('revenue')->take(10)->values();
        $topRoutes = $topRoutes->map(function ($route) use ($totalRouteRevenue) {
            $route['percentage'] = $totalRouteRevenue > 0
                ? round(($route['revenue'] / $totalRouteRevenue) * 100, 1)
                : 0;
            return $route;
        });

        // Legacy arrays for backward compatibility
        $bookedTicket['route_name'] = $topRoutes->pluck('name');
        $bookedTicket['sale_price'] = $topRoutes->pluck('revenue');

        $widget['total_bus']             = $owner->vehicles()->count();
        $widget['total_driver']          = $owner->drivers()->count();
        $widget['total_supervisor']      = $owner->supervisors()->count();
        $widget['total_coAdmin']         = $owner->coAdmins()->count();
        $widget['total_counter']         = $owner->counters()->count();
        $widget['total_counter_manager'] = $owner->counterManagers()->count();
        $widget['total_route']           = $routes->count();
        $widget['total_trip']            = $owner->trips()->count();
        $widget['active_packages']       = $owner->activePackages();

        // Today's Quick Stats
        $today = Carbon::today();
        $widget['today_revenue']    = $owner->bookedTickets()->where('status', 1)->whereDate('created_at', $today)->sum(\DB::raw('price * ticket_count'));
        $widget['today_bookings']   = $owner->bookedTickets()->where('status', 1)->whereDate('created_at', $today)->count();
        $widget['today_passengers'] = $owner->bookedTickets()->where('status', 1)->whereDate('created_at', $today)->sum('ticket_count');
        $widget['today_trips']      = $owner->trips()->active()->whereDate('date', $today)->count();

        // App specific metrics
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // App sales this month
        $appSalesThisMonth = $owner->bookedTickets()
            ->whereNotNull('passenger_id')
            ->where('status', 1)
            ->where('created_at', '>=', $thisMonth)
            ->sum(\DB::raw('price * ticket_count'));

        // App sales last month for comparison
        $appSalesLastMonth = $owner->bookedTickets()
            ->whereNotNull('passenger_id')
            ->where('status', 1)
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->sum(\DB::raw('price * ticket_count'));

        // Counter sales this month
        $counterSalesThisMonth = $owner->bookedTickets()
            ->whereNull('passenger_id')
            ->where('status', 1)
            ->where('created_at', '>=', $thisMonth)
            ->sum(\DB::raw('price * ticket_count'));

        // Counter sales last month
        $counterSalesLastMonth = $owner->bookedTickets()
            ->whereNull('passenger_id')
            ->where('status', 1)
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->sum(\DB::raw('price * ticket_count'));

        // App passenger count this month
        $appPassengersThisMonth = $owner->bookedTickets()
            ->whereNotNull('passenger_id')
            ->where('status', 1)
            ->where('created_at', '>=', $thisMonth)
            ->sum('ticket_count');

        // Revenue calculation (what operator keeps after commission)
        $commissionRate = $owner->app_commission ?? gs('app_commission');
        $appRevenueThisMonth = $appSalesThisMonth * (1 - $commissionRate / 100);
        $appRevenueLastMonth = $appSalesLastMonth * (1 - $commissionRate / 100);

        // Calculate percentage changes
        $appPercentChange = $appSalesLastMonth > 0
            ? (($appSalesThisMonth - $appSalesLastMonth) / $appSalesLastMonth) * 100
            : ($appSalesThisMonth > 0 ? 100 : 0);

        $counterPercentChange = $counterSalesLastMonth > 0
            ? (($counterSalesThisMonth - $counterSalesLastMonth) / $counterSalesLastMonth) * 100
            : ($counterSalesThisMonth > 0 ? 100 : 0);

        $appRevenuePercentChange = $appRevenueLastMonth > 0
            ? (($appRevenueThisMonth - $appRevenueLastMonth) / $appRevenueLastMonth) * 100
            : ($appRevenueThisMonth > 0 ? 100 : 0);

        $widget['app_sales']                = $appSalesThisMonth;
        $widget['app_percent_change']       = $appPercentChange;
        $widget['counter_sales']            = $counterSalesThisMonth;
        $widget['counter_percent_change']   = $counterPercentChange;
        $widget['app_passengers']           = $appPassengersThisMonth;
        $widget['app_revenue']              = $appRevenueThisMonth;
        $widget['app_revenue_percent_change'] = $appRevenuePercentChange;

        // ========================================
        // NEW PHASE 1.1: Enhanced KPIs
        // ========================================
        
        // Occupancy Rate (Today's trips average)
        $todayTrips = $owner->trips()->active()->whereDate('date', $today)->with('fleetType')->get();
        $occupancyRates = $todayTrips->map(function($trip) {
            $capacity = $trip->fleetCapacity();
            $booked = $trip->bookedCount();
            return $capacity > 0 ? ($booked / $capacity) * 100 : 0;
        });
        $widget['today_occupancy_rate'] = $occupancyRates->avg() ?? 0;
        
        // Today's Cancellations
        $widget['today_cancellations'] = $owner->bookedTickets()
            ->where('status', 0) // Cancelled status
            ->whereDate('updated_at', $today)
            ->count();
        
        // Operational Alerts - Low Occupancy Trips (next 48h, <30% booked)
        $lowOccupancyThreshold = 30; // 30%
        $upcomingTrips = $owner->trips()
            ->active()
            ->where('date', '>=', $today)
            ->where('date', '<=', $today->copy()->addDays(2))
            ->with('fleetType')
            ->get();
            
        $lowOccupancyTrips = $upcomingTrips->filter(function($trip) use ($lowOccupancyThreshold) {
            $capacity = $trip->fleetCapacity();
            $booked = $trip->bookedCount();
            $occupancy = $capacity > 0 ? ($booked / $capacity) * 100 : 0;
            return $occupancy < $lowOccupancyThreshold && $occupancy >= 0;
        });
        
        $widget['low_occupancy_count'] = $lowOccupancyTrips->count();
        $widget['low_occupancy_trips'] = $lowOccupancyTrips->take(5)->map(function($trip) {
            $capacity = $trip->fleetCapacity();
            $booked = $trip->bookedCount();
            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'date' => $trip->date->format('M d, Y'),
                'occupancy' => $capacity > 0 ? round(($booked / $capacity) * 100, 1) : 0,
                'booked' => $booked,
                'capacity' => $capacity,
            ];
        });
        
        // Vehicle Conflicts (same vehicle assigned to overlapping trips)
        $vehicleConflicts = collect();
        $assignedBuses = \App\Models\AssignedBus::whereHas('trip', function($q) use ($owner, $today) {
            $q->where('owner_id', $owner->id)
              ->where('date', '>=', $today)
              ->where('date', '<=', $today->copy()->addDays(7));
        })->with('trip', 'vehicle')->get();
        
        $vehicleGroups = $assignedBuses->groupBy('vehicle_id');
        foreach ($vehicleGroups as $vehicleId => $assignments) {
            if ($assignments->count() > 1) {
                // Check for time overlaps
                $sortedAssignments = $assignments->sortBy('trip.departure_datetime');
                for ($i = 0; $i < $sortedAssignments->count() - 1; $i++) {
                    $current = $sortedAssignments->values()[$i];
                    $next = $sortedAssignments->values()[$i + 1];
                    
                    if ($current->trip && $next->trip) {
                        $currentEnd = $current->trip->arrival_datetime ?? $current->trip->departure_datetime;
                        $nextStart = $next->trip->departure_datetime;
                        
                        if ($currentEnd && $nextStart && $currentEnd > $nextStart) {
                            $vehicleConflicts->push([
                                'vehicle' => $current->vehicle->registration_no ?? 'Unknown',
                                'trip1' => $current->trip->title,
                                'trip2' => $next->trip->title,
                            ]);
                        }
                    }
                }
            }
        }
        
        $widget['vehicle_conflicts'] = $vehicleConflicts->take(3);
        $widget['vehicle_conflict_count'] = $vehicleConflicts->count();

        return view('owner.dashboard', compact('bookedTicket', 'topRoutes', 'monthlySale', 'pageTitle', 'widget', 'owner'));

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

        // Get App sales (with passenger_id)
        $appSales = BookedTicket::where('owner_id', authUser('owner')->id)
            ->whereNotNull('passenger_id')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(price * ticket_count) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        // Get Counter sales (without passenger_id)
        $counterSales = BookedTicket::where('owner_id', authUser('owner')->id)
            ->whereNull('passenger_id')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(price * ticket_count) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];
        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'app' => getAmount($appSales->where('created_on', $date)->first()?->amount ?? 0),
                'counter' => getAmount($counterSales->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }
        $data = collect($data);
        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => __('App'),
                'data' => $data->pluck('app')
            ],
            [
                'name' => __('Counter'),
                'data' => $data->pluck('counter')
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
        $owner = authUser('owner');
        return view('owner.profile', compact('pageTitle', 'owner'));
    }

    public function profileUpdate(Request $request)
    {
        $owner = authUser('owner');
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
                $old = $owner->image;
                $owner->image = fileUploader($request->image, getFilePath('ownerProfile'), getFileSize('ownerProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $owner->firstname = $request->firstname;
        $owner->lastname  = $request->lastname;
        $owner->address   = $request->address;
        $owner->state     = $request->state;
        $owner->zip       = $request->zip;
        $owner->city      = $request->city;
        $owner->save();

        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $owner = authUser('owner');
        return view('owner.password', compact('pageTitle', 'owner'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $owner = authUser('owner');
        if (!Hash::check($request->old_password, $owner->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $owner->password = Hash::make($request->password);
        $owner->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('owner.password')->withNotify($notify);
    }

    public function settings()
    {
        $pageTitle = 'Company Information';
        $owner = authUser('owner');
        return view('owner.setting.general', compact('pageTitle', 'owner'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|',
        ]);

        $owner = authUser('owner');

        $owner->general_settings = [
            'company_name' => $request->company_name,
        ];
        $owner->save();

        $notify[] = ['success', 'General settings updated successfully.'];
        return back()->withNotify($notify);
    }

    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->owner_id = auth()->guard('owner')->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function package()
    {
        $pageTitle = 'Packages';
        $packages = Package::where('status', Status::ENABLE)->orderBy('price')->get();
        $features = Feature::latest()->get();
        $activePackage = authUser('owner')->activePackages();
        return view('owner.package.list', compact('pageTitle', 'packages', 'activePackage', 'features'));
    }

    public function packageActive()
    {
        $pageTitle = 'Active Package';
        $owner = authUser();
        $soldPackage = SoldPackage::active()->where('owner_id', $owner->id)->where('ends_at', '>', Carbon::now())->orderByDesc('ends_at')->with('package')->first();
        $features = Feature::latest()->get();
        return view('owner.package.active', compact('pageTitle', 'soldPackage', 'features'));
    }

    public function packageBuy($id)
    {
        $package   = Package::findOrFail($id);
        $startFrom = Carbon::now();
        $owner     = authUser('owner');

        $oldPackage = $owner->boughtPackages()->sortByDesc('ends_at')->first();

        if ($oldPackage) {
            $startFrom = Carbon::parse($oldPackage->ends_at);

            if ($startFrom->isPast()) {
                $startFrom = Carbon::now();
            }
        } else {
            $startFrom = Carbon::now();
        }


        $endsAt = getPackageExpireDate($package->time_limit, $package->unit, $startFrom);

        $soldPackage               = new SoldPackage();
        $soldPackage->package_id   = $package->id;
        $soldPackage->owner_id     = $owner->id;
        $soldPackage->starts_from  = $startFrom;
        $soldPackage->price        = $package->price;
        $soldPackage->ends_at      = $endsAt;
        $soldPackage->order_number = getTrx();
        $soldPackage->save();

        session()->put('order_number', $soldPackage->order_number);
        return to_route('owner.deposit.index');
    }

    public function depositHistory()
    {
        $pageTitle = 'Payment History';
        $deposits  = auth('owner')->user()->deposits()->with(['gateway'])->latest()->paginate(getPaginate());
        return view('owner.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = authUser('owner');
        if ($user->profile_complete == Status::YES) {
            return to_route('owner.dashboard');
        }

        $pageTitle  = 'Owner Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = isset($info['code']) ? implode(',', $info['code']) : '';
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('owner.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = authUser('owner');
        if ($user->profile_complete == Status::YES) {
            return to_route('owner.dashboard');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:owners|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('owners')->where('dial_code', $request->mobile_code)],
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code     = $request->country_code;
        $user->mobile           = $request->mobile;
        $user->username         = $request->username;
        $user->address          = $request->address;
        $user->city             = $request->city;
        $user->state            = $request->state;
        $user->zip              = $request->zip;
        $user->country_name     = @$request->country;
        $user->dial_code        = $request->mobile_code;
        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('owner.dashboard');
    }

    public function manageTransport()
    {
        $pageTitle       = 'Manage Transports';
        $manageTransport = json_decode(file_get_contents(resource_path('views/owner/manage_transport.json')));
        return view('owner.manage_transport', compact('pageTitle', 'manageTransport'));
    }

    public function recentAppBookings()
    {
        $owner = authUser('owner');

        // Get recent confirmed App bookings from the last 24 hours
        $recentBookings = $owner->bookedTickets()
            ->whereNotNull('passenger_id')
            ->where('status', 1) // Confirmed only
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->with(['passenger', 'trip'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $notifications = $recentBookings->map(function($booking) {
            return [
                'id' => $booking->id,
                'passenger_name' => $booking->passenger->firstname . ' ' . $booking->passenger->lastname,
                'trip_title' => $booking->trip->title,
                'seats' => count($booking->seats),
                'amount' => $booking->price,
                'time' => $booking->created_at->diffForHumans(),
                'created_at' => $booking->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $notifications->count(),
            'data' => $notifications
        ]);
    }
}
