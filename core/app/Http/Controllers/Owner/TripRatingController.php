<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\TripRating;
use Illuminate\Http\Request;

class TripRatingController extends Controller
{
    public function index()
    {
        $pageTitle = "Trip Feedbacks";
        $owner = authUser();

        // Get ratings for trips owned by this operator
        $ratings = TripRating::whereHas('trip', function($q) use ($owner) {
            $q->where('owner_id', $owner->id);
        })
        ->with(['passenger', 'trip', 'bookedTicket'])
        ->orderByDesc('id')
        ->paginate(getPaginate());

        return view('owner.feedback.index', compact('pageTitle', 'ratings'));
    }
}
