<?php

namespace App\Http\Controllers\Api\Passenger;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\TripRating;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    /**
     * List all active operators with verification status and ratings.
     */
    public function index()
    {
        $operators = Owner::active()
            ->orderByDesc('app_verified')
            ->get(['id', 'firstname', 'lastname', 'username', 'image', 'app_verified', 'general_settings']);

        $results = $operators->map(function($op) {
            $avgRating = TripRating::whereHas('trip', function($q) use ($op) {
                $q->where('owner_id', $op->id);
            })->avg('rating') ?: 0;

            return [
                'id' => $op->id,
                'name' => $op->general_settings->company_name ?? ($op->firstname . ' ' . $op->lastname),
                'logo' => $op->image ? url(getFilePath('ownerProfile') . '/' . $op->image) : null,
                'is_verified' => (bool) $op->app_verified,
                'rating' => round($avgRating, 1),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    /**
     * Show detailed operator profile.
     */
    public function show($id)
    {
        $op = Owner::where('status', 1)->findOrFail($id);
        
        $avgRating = TripRating::whereHas('trip', function($q) use ($op) {
            $q->where('owner_id', $op->id);
        })->avg('rating') ?: 0;

        $reviewsCount = TripRating::whereHas('trip', function($q) use ($op) {
            $q->where('owner_id', $op->id);
        })->count();

        // Stats: Total trips completed (approximate via booked tickets)
        $tripsCount = $op->trips()->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $op->id,
                'name' => $op->general_settings->company_name ?? ($op->firstname . ' ' . $op->lastname),
                'logo' => $op->image ? url(getFilePath('ownerProfile') . '/' . $op->image) : null,
                'is_verified' => (bool) $op->app_verified,
                'rating' => round($avgRating, 1),
                'total_reviews' => $reviewsCount,
                'total_trips' => $tripsCount,
                'joined_at' => $op->created_at->format('M Y'),
                'address' => $op->address,
                'city' => $op->city,
                'state' => $op->state,
            ]
        ]);
    }

    /**
     * Get reviews for a specific operator.
     */
    public function reviews($id)
    {
        $reviews = TripRating::whereHas('trip', function($q) use ($id) {
            $q->where('owner_id', $id);
        })->with(['passenger', 'trip'])
          ->latest()
          ->paginate(10);

        $results = $reviews->map(function($rev) {
            return [
                'id' => $rev->id,
                'passenger_name' => $rev->passenger->firstname . ' ' . $rev->passenger->lastname,
                'rating' => $rev->rating,
                'comment' => $rev->comment,
                'trip_title' => $rev->trip->title,
                'date' => $rev->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $results,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }
}
