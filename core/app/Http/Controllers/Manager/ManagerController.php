<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Counter;
use App\Constants\Status;
use App\Models\TicketPrice;
use App\Models\BookedTicket;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\TicketPriceByStoppage;

class ManagerController extends Controller
{
    public function sell()
    {
        $pageTitle = 'Book Ticket';
        $manager = authUser('manager');
        $activePackage = $manager->owner->activePackages();
        $counters = $manager->owner->counters()->get();

        return view('manager.sell.index', compact('pageTitle', 'activePackage', 'counters'));
    }

    public function searchTrip(Request $request)
    {
        $request->validate([
            'date_of_journey' => 'required|date|date_format:m/d/Y|after:yesterday',
            'from'            => 'required|integer|gt:0',
            'to'              => 'required|integer|gt:0'
        ]);

        $owner = authUser('manager')->owner;

        $dayOff   =  Carbon::parse($request->date_of_journey)->format('w');
        $sdArray = [$request->from, $request->to];
        $routes   = $owner->routes()->active()
            ->searchable(['name'])
            ->whereJsonContains('stoppages', $sdArray)
            ->with(['ticketPrice', 'ticketPrice.prices', 'trips' => function ($trip) use ($request) {
                return $trip->where('status', 1)->whereDate('date', Carbon::parse($request->date_of_journey));
            }, 'trips.schedule', 'trips.bookedTickets' => function ($q) use ($request) {
                return $q->where('date_of_journey', $request->date_of_journey);
            }])
            ->paginate(getPaginate());

        $pageTitle = "Available Trips";

        return view('manager.trip.index', compact('pageTitle', 'routes', 'sdArray', 'owner'));
    }

    public function book($ticketPriceId, $id)
    {
        $owner = authUser('manager')->owner;
        $trip = $owner->trips()->where('id', $id)->with('route', 'fleetType', 'schedule', 'bookedTickets')->first();
        $route = $trip->route;
        $stoppages = $trip->route->stoppages;

        if ($route->starting_point == $trip->starting_point && $route->destination_point == $trip->destination_point) {
            $reverse = false;
        } else {
            $reverse = true;
        }

        $bookedTickets = $trip->bookedTickets->where('date_of_journey', Carbon::now()->format('Y-m-d'));
        $ticketPrices  = TicketPriceByStoppage::where('ticket_price_id', $ticketPriceId)->get();
        $stoppageArr = $trip->route->stoppages;

        if ($trip->owner_id != $owner->id) abort(401);

        $pageTitle = 'Book Ticket of - ' . $trip->title;
        $stoppages = Counter::routeStoppages($stoppageArr)->sortBy('name');

        return view('manager.trip.book', compact('trip', 'pageTitle', 'stoppages', 'ticketPrices', 'bookedTickets', 'reverse', 'owner', 'ticketPriceId'));
    }

    public function bookByDate($id)
    {
        $date = request()->date;
        $owner = authUser('manager')->owner;
        $trip = $owner->trips()->where('id', $id)->first();
        $bookedTickets = $trip->bookedTickets->where('date_of_journey', Carbon::parse($date)->format('Y-m-d'));

        return response()->json(['bookedTickets' => $bookedTickets]);
    }

    function getTicketPrice(Request $request)
    {
        $ticketPrice = TicketPrice::where('route_id', $request->route_id)->where('fleet_type_id', $request->fleet_type_id)->with('route')->first();
        $route = $ticketPrice->route;
        $stoppages = $ticketPrice->route->stoppages;
        $trip = Trip::find($request->trip_id);
        $sourcePos = array_search($request->source_id, $stoppages);
        $destinationPos = array_search($request->destination_id, $stoppages);

        $owner = authUser('manager')->owner;

        $booked_ticket  = $owner->bookedTickets()->where('trip_id', $trip->id)->where('date_of_journey', Carbon::parse($request->date)->format('Y-m-d'))->get()->toArray();

        if ($route->starting_point == $trip->starting_point && $route->destination_point == $trip->destination_point) {
            $reverse = false;
        } else {
            $reverse = true;
        }

        if (!$reverse) {
            $canGo = ($sourcePos < $destinationPos) ? true : false;
        } else {
            $canGo = ($sourcePos > $destinationPos) ? true : false;
        }

        if (!$canGo) {
            $data = [
                'error' => 'Select Pickup Point & Dropping Point Properly'
            ];
            return response()->json($data);
        }
        $sdArray = [$request->source_id, $request->destination_id];
        $getPrice = $ticketPrice->prices()->where('source_destination', json_encode($sdArray))->orWhere('source_destination', json_encode(array_reverse($sdArray)))->first();

        if ($getPrice) {
            $price = $getPrice->price;
        } else {
            $price = [
                'error' => 'Admin may not set prices for this route. So, you can\'t sell ticket for this trip. Please contact with admin to set prices'
            ];
        }
        $data['bookedSeats']        = $booked_ticket;
        $data['req_source']         = $request->source_id;
        $data['req_destination']    = $request->destination_id;
        $data['reverse']            = $reverse;
        $data['stoppages']          = $stoppages;
        $data['price']              = $price;

        return response()->json($data);
    }

    public function booked(Request $request, $id)
    {
        $request->validate([
            "pick_up_point"   => "required|integer|gt:0",
            "dropping_point"  => "required|integer|gt:0",
            "price"           => "required|numeric|gt:0",
            "name"            => "required|string",
            "mobile_number"   => "required|string",
            "email"           => "nullable|email",
            "seat_number"     => "required|string",
            "gender"          => "required|integer|in:0,1,2",
            "date_of_journey" => "required|date"
        ], [
            "seat_number.required" => "Please Select at Least One Seat",
        ]);

        $dateOfJourney = Carbon::parse($request->date_of_journey);
        $today = Carbon::today()->format('Y-m-d');
        if ($dateOfJourney->format('Y-m-d') < $today) {
            $notify[] = ['error', 'Date of journey cant\'t be less than today.'];
            return redirect()->back()->withNotify($notify);
        }
        $dayOff = $dateOfJourney->format('w');
        $owner = authUser('manager')->owner;
        $trip = $owner->trips()->findOrFail($id);



        $trip = Trip::find($id);
        $route = $trip->route;
        $stoppages = $trip->route->stoppages;
        $sourcePos = array_search($request->pick_up_point, $stoppages);
        $destinationPos = array_search($request->dropping_point, $stoppages);

        $bookedTicket = $owner->bookedTickets()->where('trip_id', $id)
            ->where('date_of_journey', $dateOfJourney->format('Y-m-d'))
            ->where('pick_up_point', $request->pick_up_point)
            ->where('dropping_point', $request->dropping_point)
            ->whereJsonContains('seats', $request->seat_number)
            ->get();

        if (empty($bookedTicket)) {
            $notify[] = ['error', 'Why you are choosing those seats which are already booked?'];
            return back()->withNotify($notify);
        }

        if ($route->starting_point == $trip->starting_point && $route->destination_point == $trip->destination_point) {
            $reverse = false;
        } else {
            $reverse = true;
        }

        if (!$reverse) {
            $canGo = ($sourcePos < $destinationPos) ? true : false;
        } else {
            $canGo = ($sourcePos > $destinationPos) ? true : false;
        }

        if (!$canGo) {
            $notify[] = ['error', 'Select Pickup Point & Dropping Point Properly'];
            return back()->withNotify($notify);
        }

        $sourceDestination = [$request->pick_up_point, $request->dropping_point];
        $sdInfo = Counter::routeStoppages($sourceDestination);

        $passenger['name']          = $request->name;
        $passenger['mobile_number'] = $request->mobile_number;
        $passenger['email']         = $request->email;
        $passenger['gender']        = $request->gender;
        $passenger['from']          = $sdInfo[0]->name;
        $passenger['to']            = $sdInfo[1]->name;

        $seats = (explode(',', $request->seat_number));
        $ticketPrice = TicketPrice::where('route_id', $trip->route_id)->where('fleet_type_id', $trip->fleet_type_id)->first();
        $sdArray = [$request->pick_up_point, $request->dropping_point];
        $getPrice = $ticketPrice->prices()
            ->where('source_destination', json_encode($sdArray))
            ->orWhere('source_destination', json_encode(array_reverse($sdArray)))
            ->first();

        $bookedTicket                     = new BookedTicket();
        $bookedTicket->owner_id           = $owner->id;
        $bookedTicket->counter_manager_id = authUser('manager')->id;
        $bookedTicket->trip_id            = $id;
        $bookedTicket->pick_up_point      = $request->pick_up_point;
        $bookedTicket->dropping_point     = $request->dropping_point;
        $bookedTicket->source_destination = $sourceDestination;
        $bookedTicket->price              = $getPrice->price;
        $bookedTicket->passenger_details  = $passenger;
        $bookedTicket->ticket_count       = sizeof($seats);
        $bookedTicket->seats              = $seats;
        $bookedTicket->date_of_journey    = $dateOfJourney->format('Y-m-d');
        $bookedTicket->save();

        $notify[] = ['success', 'Ticket Booked Successfully'];
        return to_route('manager.sell.ticket.print', $bookedTicket->id)->withNotify($notify);
    }

    public function ticketPrint($id)
    {
        $ticket = BookedTicket::with('trip', 'trip.route', 'trip.schedule', 'trip.owner')->findOrFail($id);
        $pageTitle = 'Print Ticket';
        return view('manager.trip.ticket', compact('ticket', 'pageTitle'));
    }

    public function trips()
    {
        $pageTitle = 'All Trips';
        $owner = authUser('manager')->owner;
        $activePackage = $owner->activePackages();
        $trips = $owner->trips()
            ->selectRaw('trips.*, ticket_prices.id as ticket_price_id')
            ->join('ticket_prices', function ($join) {
                $join->on('trips.route_id', '=', 'ticket_prices.route_id')
                    ->on('trips.fleet_type_id', '=', 'ticket_prices.fleet_type_id')
                    ->where('trips.status', Status::ENABLE);
            })->with('schedule')
            ->whereHas('route')
            ->paginate(getPaginate());

        return view('manager.trip.index', compact('pageTitle', 'trips', 'activePackage', 'owner'));
    }

    public function statistics()
    {
        $pageTitle = 'Statistics';
        $manager = authUser('manager');
        $owner = $manager->owner;

        $monthlySales = $manager->bookedTickets()
            ->whereMonth('created_at', date('m'))
            ->active()
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('F d');
            });

        $yearlySales = $manager->bookedTickets()
            ->whereYear('created_at', date('Y'))
            ->active()
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('F');
            });

        $dailySale = $manager->bookedTickets()
            ->active()
            ->whereDate('created_at', date('Y-m-d'))
            ->selectRaw('sum(price*ticket_count) AS total_sales , sum(ticket_count) as total_ticket')
            ->first();

        $allSale = $manager->bookedTickets()
            ->active()
            ->selectRaw('sum(price*ticket_count) AS total_sales , sum(ticket_count) as total_ticket')
            ->first();

        $monthlySale = [];
        $yearlySale = [];

        $monthlyTicketCount = 0;
        foreach ($monthlySales as $key => $value) {
            $sPrice = 0;
            foreach ($value as $item) {
                $sPrice += $item->price * $item->ticket_count;
                $monthlyTicketCount += $item->ticket_count;
            }
            $monthlySale[$key] = $sPrice;
        }

        $yearlyTicketCount = 0;
        foreach ($yearlySales as $key => $value) {
            $sPrice = 0;
            foreach ($value as $item) {
                $sPrice += $item->price * $item->ticket_count;
                $yearlyTicketCount += $item->ticket_count;
            }
            $yearlySale[$key] = $sPrice;
        }

        return view('manager.statistics', compact('pageTitle', 'monthlySale', 'monthlyTicketCount', 'dailySale', 'yearlySale', 'yearlyTicketCount', 'owner', 'allSale'));
    }

    public function todaysSold()
    {
        $pageTitle = 'Today\'s Sold Tickets';
        $manager = authUser('manager');
        $owner = $manager->owner;
        $routes = $owner->routes()->active()->get();
        $trips = $owner->trips()->active()->get();

        $soldTickets = $owner->bookedTickets()
            ->searchable(['id'])
            ->with('trip', 'trip.route', 'counterManager')
            ->where('counter_manager_id', $manager->id)
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('manager.sold_tickets.list', compact('pageTitle', 'soldTickets', 'routes', 'trips', 'owner'));
    }

    public function cancelSold(Request $request, $id)
    {
        $now = Carbon::now();
        $bookedTicket = BookedTicket::whereId($id)->with('trip', 'trip.schedule')->firstOrFail();
        $tripTime = Carbon::parse($bookedTicket->date_of_journey . ' ' . $bookedTicket->trip->schedule->starts_from);

        if ($bookedTicket->date_of_journey < $now && $tripTime->diffInHours($now) > 6) {
            $notify[] = ['error', 'Sorry cancellation time is over'];
            return back()->withNotify($notify);
        }
        if ($bookedTicket->status == Status::DISABLE) {
            $bookedTicket->status = Status::ENABLE;
            $bookedTicket->save();
            $message = 'Booking added successfully';
        }else{
            $bookedTicket->status = Status::DISABLE;
            $bookedTicket->save();
            $message = 'Booking Canceled Successfully';
        }

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function allSold()
    {
        $pageTitle = 'All Sold Tickets';
        $manager = authUser('manager');
        $owner = $manager->owner;
        $routes = $owner->routes()->active()->get();
        $trips = $owner->trips()->active()->get();

        $soldTickets = $owner->bookedTickets()
            ->searchable(['id'])
            ->filter(['trip:route_id', 'trip_id', 'date_of_journey'])
            ->with('trip', 'trip.route', 'counterManager')
            ->where('counter_manager_id', $manager->id)
            ->orderByDesc('id');

        if (request()->created_at) {
            $soldTickets->whereDate('created_at', Carbon::parse(request()->booking_date));
        }

        $soldTickets = $soldTickets->paginate(getPaginate());

        return view('manager.sold_tickets.list', compact('soldTickets', 'pageTitle', 'routes', 'trips', 'owner'));
    }

    public function cancelledSold()
    {
        $pageTitle = 'Canceled Tickets';
        $manager = authUser('manager');
        $owner = $manager->owner;
        $routes = $owner->routes()->active()->get();
        $trips = $owner->trips()->active()->get();

        $soldTickets = $owner->canceledTickets()
            ->searchable(['id'])
            ->with('trip', 'trip.route', 'counterManager')
            ->where('counter_manager_id', $manager->id)
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('manager.sold_tickets.list', compact('pageTitle', 'soldTickets', 'owner', 'routes', 'trips'));
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $manager = authUser('manager');
        return view('manager.profile', compact('pageTitle', 'manager'));
    }

    public function profileUpdate(Request $request)
    {
        $manager = authUser('manager');
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
                $old = $manager->image;
                $manager->image = fileUploader($request->image, getFilePath('counter_manager'), getFileSize('counter_manager'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $manager->firstname = $request->firstname;
        $manager->lastname  = $request->lastname;
        $manager->address   = $request->address;
        $manager->state     = $request->state;
        $manager->zip       = $request->zip;
        $manager->city      = $request->city;
        $manager->save();

        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $manager = authUser('manager');
        return view('manager.password', compact('pageTitle', 'manager'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $manager = authUser('manager');
        if (!Hash::check($request->old_password, $manager->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $manager->password = Hash::make($request->password);
        $manager->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('manager.password')->withNotify($notify);
    }
}
