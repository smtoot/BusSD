<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Services\TripPricingService;
use Illuminate\Http\Request;

class TripPricingController extends Controller
{
    protected $pricingService;
    
    public function __construct(TripPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }
    
    /**
     * Get pricing preview/breakdown for a trip
     *
     * @param Request $request
     * @param int $tripId
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        // Ensure owner can only access their trips
        if ($trip->owner_id !== authUser()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $breakdown = $this->pricingService->getPriceBreakdown($trip, [
            'occupancy' => $request->input('occupancy'),
            'day_of_week' => $request->input('day_of_week'),
        ]);
        
        return response()->json($breakdown);
    }
    
    /**
     * Get pricing suggestion for a trip
     *
     * @param Request $request
     * @param int $tripId
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        if ($trip->owner_id !== authUser()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $suggestedPrice = $this->pricingService->suggestPrice($trip);
        
        return response()->json([
            'current_price' => (float) $trip->price,
            'suggested_price' => $suggestedPrice,
            'difference' => round($suggestedPrice - $trip->price, 2),
            'percentage_change' => $trip->price > 0 
                ? round((($suggestedPrice - $trip->price) / $trip->price) * 100, 2)
                : 0,
        ]);
    }
    
    /**
     * Get applicable pricing rules for a trip
     *
     * @param int $tripId
     * @return \Illuminate\Http\JsonResponse
     */
    public function rules($tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        if ($trip->owner_id !== authUser()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $rules = $this->pricingService->getApplicableRules($trip);
        
        return response()->json([
            'trip_id' => $trip->id,
            'trip_title' => $trip->title,
            'rules_count' => $rules->count(),
            'rules' => $rules->map(function($rule) {
                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'type' => $rule->modifier_type,
                    'value' => $rule->modifier_value,
                    'priority' => $rule->priority,
                ];
            }),
        ]);
    }
}
