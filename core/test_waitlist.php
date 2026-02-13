<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Passenger;
use App\Models\Trip;
use App\Models\Waitlist;
use App\Models\SeatLock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Helper to simulate request
function makeRequest($method, $uri, $user, $data = []) {
    $req = Illuminate\Http\Request::create($uri, $method, $data);
    $req->setUserResolver(function () use ($user) {
        return $user;
    });
    return app()->handle($req);
}

// 1. Setup Data
$passenger = Passenger::where('status', 1)->first();
if (!$passenger) {
    die("No active passenger found.\n");
}
$trip = Trip::active()->first();
if (!$trip) {
    die("No active trip found.\n");
}
$date = Carbon::tomorrow()->format('Y-m-d');
$pickup = 1; // Dummy ID
$drop = 2; // Dummy ID

echo "Testing with Passenger ID: {$passenger->id}, Trip ID: {$trip->id}, Date: $date\n";

// 2. Clean previous data
Waitlist::where('passenger_id', $passenger->id)->delete();
SeatLock::where('passenger_id', $passenger->id)->delete();

// 3. Join Waitlist
echo "\n--- Joining Waitlist ---\n";
$controller = new \App\Http\Controllers\Api\Passenger\BookingController();
$request = Illuminate\Http\Request::create('/api/booking/waitlist/join', 'POST', [
    'trip_id' => $trip->id,
    'date' => $date,
    'pickup_id' => $pickup,
    'destination_id' => $drop,
    'seat_count' => 1
]);
$request->setUserResolver(fn() => $passenger);
$response = $controller->joinWaitlist($request);
echo "Response: " . $response->getContent() . "\n";

// 4. Check Status
echo "\n--- Checking Waitlist Status ---\n";
$requestStatus = Illuminate\Http\Request::create('/api/booking/waitlist/status', 'GET', [
    'trip_id' => $trip->id,
    'date' => $date,
]);
$requestStatus->setUserResolver(fn() => $passenger);
$responseStatus = $controller->checkWaitlistStatus($requestStatus);
echo "Response: " . $responseStatus->getContent() . "\n";

// 5. Simulate Seat Lock Expiry and Release
echo "\n--- Simulating Manual Release to Trigger Notification ---\n";
// Create a fake lock for another user
$otherUser = Passenger::where('id', '!=', $passenger->id)->first();
if ($otherUser) {
    SeatLock::create([
        'trip_id' => $trip->id,
        'passenger_id' => $otherUser->id,
        'date_of_journey' => Carbon::parse($date)->format('m/d/Y'), // Model stores as m/d/Y usually if not cast? No, model casts to datetime but DB stores date or string? Controller uses m/d/Y format for checks.
        'seats' => ['1'],
        'expires_at' => Carbon::now()->addMinutes(10)
    ]);
    
    // Call releaseSeats as other user
    $requestRelease = Illuminate\Http\Request::create('/api/booking/release-seats', 'POST', [
        'trip_id' => $trip->id,
        'date' => $date,
    ]);
    $requestRelease->setUserResolver(fn() => $otherUser);
    $responseRelease = $controller->releaseSeats($requestRelease);
    echo "Release Response: " . $responseRelease->getContent() . "\n";
    
    // Check if status changed to 1 (Notified)
    $updatedWaitlist = Waitlist::where('passenger_id', $passenger->id)->first();
    echo "Updated Waitlist Status: " . $updatedWaitlist->status . " (Expected: 1)\n";
} else {
    echo "Skipping release test (need 2nd passenger)\n";
}
