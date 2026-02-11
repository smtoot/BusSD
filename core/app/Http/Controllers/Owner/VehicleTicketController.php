<?php

namespace App\Http\Controllers\Owner;

use App\Models\FleetType;
use App\Models\TicketPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\TicketPriceByStoppage;

class VehicleTicketController extends Controller
{
    public function index()
    {
        $owner = authUser();
        $ticketPrices = TicketPrice::where('owner_id', $owner->id)
            ->with(['route', 'fleetType'])
            ->whereHas('fleetType')
            ->whereHas('route')
            ->groupBy('route_id')
            ->groupBy('fleet_type_id')
            ->orderByDesc('id')
            ->paginate(getPaginate());
        $pageTitle = 'All Ticket Price List';

        return view('owner.vehicle_tickets.index', compact('pageTitle', 'ticketPrices'));
    }

    public function create()
    {
        $pageTitle = 'Set Ticket Price';
        $owner = authUser();
        $fleetTypes = FleetType::where('owner_id', $owner->id)->active()->get();
        $routes = Route::active()->where('owner_id', $owner->id)->get();
        return view('owner.vehicle_tickets.create', compact('pageTitle', 'fleetTypes', 'routes', 'owner'));
    }

    public function edit($id)
    {
        $owner = authUser();
        $ticketPrices = TicketPrice::where('owner_id', $owner->id)
            ->with(['prices', 'route', 'fleetType'])
            ->findOrFail($id);
        $pageTitle     = 'Ticket Prices for ' . $ticketPrices->route->name . ' (' . $ticketPrices->fleetType->name . ')';
        return view('owner.vehicle_tickets.edit', compact('pageTitle', 'ticketPrices', 'owner'));
    }

    public function ticketPriceStore(Request $request)
    {
        $request->validate([
            'fleet_type' => 'required|integer|gt:0|exists:fleet_types,id',
            'route'      => 'required|integer|gt:0|exists:routes,id',
            'main_price' => 'required|numeric|gt:0',
            'price'      => 'sometimes|required|array|min:1',
            'price.*'    => 'sometimes|required|numeric|gte:0',
        ], [
            'main_price'       => 'Price for Source to Destination',
            'price.*.required' => 'All Price Fields are Required',
            'price.*.numeric'  => 'All Price Fields Should Be a Number',
        ]);

        $owner = authUser();
        $ticketPrice = TicketPrice::where('owner_id', $owner->id)->where('route_id', $request->route)->where('fleet_type_id', $request->fleet_type)->first();

        if ($ticketPrice) {
            $notify[] = ['error', 'You have already added prices for this fleet'];
            return back()->withNotify($notify);
        }

        $ticketPrice                = new TicketPrice();
        $ticketPrice->owner_id      = $owner->id;
        $ticketPrice->route_id      = $request->route;
        $ticketPrice->fleet_type_id = $request->fleet_type;
        $ticketPrice->main_price    = $request->main_price;
        $ticketPrice->save();

        foreach ($request->price as $key => $price) {
            $idArray = explode('-', $key);
            $ticketPriceByStoppage                     = new TicketPriceByStoppage();
            $ticketPriceByStoppage->ticket_price_id    = $ticketPrice->id;
            $ticketPriceByStoppage->source_destination = $idArray;
            $ticketPriceByStoppage->price              = $price;
            $ticketPriceByStoppage->save();
        }

        $notify[] = ['success', 'All prices added successfully'];
        return back()->withNotify($notify);
    }

    public function updatePrices(Request $request, $id)
    {
        $owner = authUser();
        $ticketPriceByStoppage = TicketPriceByStoppage::findOrFail($id);

        if ($ticketPriceByStoppage->mainPrice->owner_id != $owner->id) abort(401);

        $request->validate([
            'price'   => 'required|numeric|gt:0',
        ]);

        $ticketPriceByStoppage->price = $request->price;
        $ticketPriceByStoppage->save();

        $notify = ['success' => true, 'message' => 'Price Updated Successfully'];
        return response()->json($notify);
    }

    public function checkTicketPrice(Request $request)
    {
        $owner = authUser();
        $ticketPrice = TicketPrice::where('owner_id', $owner->id)
            ->where('route_id', $request->route_id)
            ->where('fleet_type_id', $request->fleet_type_id)
            ->first();
        if (!$ticketPrice) {
            // Get route and its stoppages for inline pricing
            $route = Route::where('owner_id', $owner->id)->active()->findOrFail($request->route_id);
            $stoppages = array_values($route->stoppages);
            $stoppageCombinations = stoppageCombination($stoppages, 2);
            
            return response()->json([
                'needs_pricing' => true,
                'route' => $route,
                'stoppages' => $stoppageCombinations
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    public function getRouteData(Request $request)
    {
        $owner = authUser();
        $route = Route::where('owner_id', $owner->id)->active()->findOrFail($request->route_id);
        $ticketPrice = TicketPrice::where('owner_id', $owner->id)->where('route_id', $request->route_id)->where('fleet_type_id', $request->fleet_type_id)->first();
        if ($ticketPrice) {
            return response()->json(['error' => trans('You have added prices for this fleet type on this route')]);
        }
        $stoppages  = array_values($route->stoppages);
        $stoppages  = stoppageCombination($stoppages, 2);
        return view('owner.vehicle_tickets.route_data', compact('stoppages', 'route', 'owner'));
    }

    public function changeTicketPriceStatus($id)
    {
        return TicketPrice::changeStatus($id);
    }
}
