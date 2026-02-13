<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeatLock;
use App\Models\Waitlist;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release expired seat locks and notify waitlisted users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredLocks = SeatLock::where('expires_at', '<', Carbon::now())->get();

        if ($expiredLocks->isEmpty()) {
            $this->info('No expired locks found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredLocks->count()} expired locks. Processing...");

        // Group by trip to minimize queries
        $tripsToNotify = [];

        foreach ($expiredLocks as $lock) {
            $tripId = $lock->trip_id;
            $dateOfJourney = $lock->date_of_journey;
            
            // Delete the lock
            $lock->delete();
            
            $key = "{$tripId}_{$dateOfJourney}";
            if (!isset($tripsToNotify[$key])) {
                $tripsToNotify[$key] = [
                    'trip_id' => $tripId,
                    'date_of_journey' => $dateOfJourney
                ];
            }
        }

        // Process waitlists for affecting trips
        foreach ($tripsToNotify as $key => $data) {
            $this->processWaitlist($data['trip_id'], $data['date_of_journey']);
        }

        $this->info('Expired inventory released successfully.');
        return Command::SUCCESS;
    }

    protected function processWaitlist($tripId, $dateOfJourney)
    {
        // Find pending waitlist entries for this trip/date
        $waitlistEntries = Waitlist::where('trip_id', $tripId)
            ->where('date_of_journey', $dateOfJourney)
            ->where('status', 0) // Pending
            ->orderBy('created_at') // First come first serve
            ->get();

        if ($waitlistEntries->isEmpty()) {
            return;
        }

        // Check current availability
        $trip = Trip::find($tripId);
        if (!$trip) return;

        // In a real scenario, we'd check exact seat availability here.
        // For now, we'll notify the top N users on the waitlist based on roughly how many seats might have opened up.
        // A simple approach is to notify the first few people that "Seats are available!"
        
        foreach ($waitlistEntries as $entry) {
            // Notify user
            $this->notifyUser($entry);
            
            // Mark as notified
            $entry->status = 1;
            $entry->save();
            
            Log::info("Notified passenger {$entry->passenger_id} about availability for trip {$tripId}");
        }
    }

    protected function notifyUser($waitlistEntry)
    {
        // Placeholder for actual notification logic (FCM, SMS, Email)
        // For now, we'll just log it, or rely on the status change to show in the app
        $passenger = $waitlistEntry->passenger;
        if ($passenger) {
           notify($passenger, 'SEAT_AVAILABLE', [
               'trip_route' => $waitlistEntry->trip->route->name ?? 'Trip',
               'date' => $waitlistEntry->date_of_journey
           ]);
        }
    }
}
