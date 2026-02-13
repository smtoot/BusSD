<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SeatPricingModifier;
use App\Models\Trip;
use App\Models\FleetType;
use Illuminate\Http\Request;

class SeatPricingController extends Controller
{
    /**
     * Display list of seat pricing modifiers
     */
    public function index()
    {
        $pageTitle = "Seat Pricing Modifiers";
        $owner = authUser();
        
        $modifiers = SeatPricingModifier::where('owner_id', $owner->id)
            ->with(['trip:id,title', 'fleetType:id,name'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(getPaginate());
        
        return view('owner.seat_pricing.index', compact('pageTitle', 'modifiers'));
    }
    
    /**
     * Show form for creating/editing modifier
     */
    public function form($id = null)
    {
        $pageTitle = $id ? "Edit Seat Pricing Modifier" : "Create Seat Pricing Modifier";
        $owner = authUser();
        
        $modifier = $id ? SeatPricingModifier::findOrFail($id) : null;
        
        // Authorization check
        if ($modifier && $modifier->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->route('owner.seat.pricing.index')->withNotify($notify);
        }
        
        // Get trips and fleet types for dropdowns
        $trips = Trip::where('owner_id', $owner->id)
            ->active()
            ->orderBy('title')
            ->get();
            
        $fleetTypes = FleetType::where('owner_id', $owner->id)
            ->active()
            ->orderBy('name')
            ->get();
        
        return view('owner.seat_pricing.form', compact('pageTitle', 'modifier', 'trips', 'fleetTypes'));
    }
    
    /**
     * Store or update seat pricing modifier
     */
    public function store(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'modifier_type' => 'required|in:percentage,fixed',
            'modifier_value' => 'required|numeric',
            'applies_to' => 'required|in:category,position,specific_seats,all',
            'priority' => 'nullable|integer|min:0|max:100',
            'row_range_start' => 'nullable|integer|min:1',
            'row_range_end' => 'nullable|integer|min:1',
            'seat_type' => 'nullable|in:window,aisle,middle',
        ]);
        
        $owner = authUser();
        
        $modifier = $id ? SeatPricingModifier::findOrFail($id) : new SeatPricingModifier();
        
        // Authorization check
        if ($modifier->exists && $modifier->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->route('owner.seat.pricing.index')->withNotify($notify);
        }
        
        // Fill basic fields
        $modifier->fill($request->only([
            'name', 'description', 'modifier_type', 'modifier_value',
            'applies_to', 'seat_category', 'row_range_start', 'row_range_end', 
            'seat_type', 'priority'
        ]));
        
        $modifier->owner_id = $owner->id;
        $modifier->trip_id = $request->trip_id ?: null;
        $modifier->fleet_type_id = $request->fleet_type_id ?: null;
        $modifier->is_active = $request->has('is_active');
        
        // Parse seat positions for specific_seats type
        if ($request->applies_to === 'specific_seats' && $request->seat_positions_text) {
            $seatPositions = array_map('trim', explode(',', $request->seat_positions_text));
            $modifier->seat_positions = array_filter($seatPositions); // Remove empty values
        } else {
            $modifier->seat_positions = null;
        }
        
        $modifier->save();
        
        $message = $id ? 'Seat pricing modifier updated successfully' : 'Seat pricing modifier created successfully';
        $notify[] = ['success', $message];
        
        return redirect()->route('owner.seat.pricing.index')->withNotify($notify);
    }
    
    /**
     * Toggle active status
     */
    public function status($id)
    {
        $owner = authUser();
        $modifier = SeatPricingModifier::findOrFail($id);
        
        // Authorization check
        if ($modifier->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->back()->withNotify($notify);
        }
        
        $modifier->is_active = !$modifier->is_active;
        $modifier->save();
        
        $status = $modifier->is_active ? 'activated' : 'deactivated';
        $notify[] = ['success', "Modifier {$status} successfully"];
        
        return redirect()->back()->withNotify($notify);
    }
    
    /**
     * Delete modifier
     */
    public function delete($id)
    {
        $owner = authUser();
        $modifier = SeatPricingModifier::findOrFail($id);
        
        // Authorization check
        if ($modifier->owner_id !== $owner->id) {
            $notify[] = ['error', 'Unauthorized access'];
            return redirect()->back()->withNotify($notify);
        }
        
        $modifier->delete();
        
        $notify[] = ['success', 'Modifier deleted successfully'];
        return redirect()->back()->withNotify($notify);
    }
    
    /**
     * Preview seat pricing for a trip (AJAX)
     */
    public function preview(Request $request, $tripId)
    {
        $owner = authUser();
        
        // If tripId is 0 (new trip), we try to use fleet_type or just mock it
        if ($tripId == 0) {
            // Logic for previewing on a non-existent trip (new form)
            // For now, we return empty or use a default fleet if provided in request
            return response()->json(['html' => '<p class="text-muted">@lang("Save trip draft first to preview seat-level premiums.")</p>']);
        }

        $trip = Trip::where('owner_id', $owner->id)->with('fleetType.seatLayout')->findOrFail($tripId);
        
        $pricingService = app(\App\Services\TripPricingService::class);
        $seatPricing = $pricingService->getAllSeatsPricing($trip);
        
        $html = view('owner.seat_pricing.partials.preview', [
            'trip' => $trip,
            'seats' => $seatPricing
        ])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    }
}
