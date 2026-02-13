<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Trip;
use Carbon\Carbon;

class TripGenerationService
{
    /**
     * Generate trips for a specific schedule.
     *
     * @param Schedule $schedule
     * @param int $days Number of days to generate ahead (default 30)
     * @return int Number of trips generated
     */
    public function generateForSchedule(Schedule $schedule, $days = 30)
    {
        $generatedCount = 0;
        $startDate = $schedule->starts_on ? Carbon::parse($schedule->starts_on) : Carbon::today();
        
        // If start date is in the past, start from today
        if ($startDate->isPast()) {
            $startDate = Carbon::today();
        }

        $endDate = $schedule->never_ends 
            ? Carbon::today()->addDays($days) 
            : ($schedule->ends_on ? Carbon::parse($schedule->ends_on) : Carbon::today()->addDays($days));

        // Ensure we don't exceed the requested look-ahead unless it's a fixed end date
        if ($schedule->never_ends && $endDate->gt(Carbon::today()->addDays($days))) {
            $endDate = Carbon::today()->addDays($days);
        }

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            if ($this->shouldGenerateOnDate($schedule, $currentDate)) {
                if ($this->createTripInstance($schedule, $currentDate)) {
                    $generatedCount++;
                }
            }
            $currentDate->addDay();
        }

        return $generatedCount;
    }

    /**
     * Check if a trip should be generated on a specific date based on schedule recurrence.
     */
    protected function shouldGenerateOnDate(Schedule $schedule, Carbon $date)
    {
        if ($schedule->recurrence_type == 'daily') {
            return true;
        }

        if ($schedule->recurrence_type == 'weekly') {
            $days = $schedule->recurrence_days ?? [];
            return in_array($date->dayOfWeek, $days);
        }

        return false;
    }

    /**
     * Create a trip instance if it doesn't exist.
     */
    protected function createTripInstance(Schedule $schedule, Carbon $date)
    {
        $existing = Trip::where('schedule_id', $schedule->id)
            ->where('date', $date->format('Y-m-d'))
            ->first();

        if ($existing) {
            return false;
        }

        $trip = new Trip();
        $trip->owner_id = $schedule->owner_id;
        $trip->title = $this->generateTitle($schedule, $date);
        $trip->fleet_type_id = $schedule->fleet_type_id;
        $trip->route_id = $schedule->route_id;
        $trip->schedule_id = $schedule->id;
        $trip->starting_point = $schedule->starting_point;
        $trip->destination_point = $schedule->destination_point;
        $trip->date = $date->format('Y-m-d');
        
        // RedBus Template Fields
        $trip->trip_type = $schedule->trip_type ?? 'local';
        $trip->trip_category = $schedule->trip_category ?? 'standard';
        $trip->bus_type = $schedule->bus_type;
        $trip->base_price = $schedule->base_price ?? 0;
        $trip->weekend_surcharge = $schedule->weekend_surcharge ?? 0;
        $trip->holiday_surcharge = $schedule->holiday_surcharge ?? 0;
        $trip->early_bird_discount = $schedule->early_bird_discount ?? 0;
        $trip->last_minute_surcharge = $schedule->last_minute_surcharge ?? 0;
        $trip->search_priority = $schedule->search_priority ?? 50;
        $trip->trip_status = $schedule->trip_status ?? 'draft';
        $trip->status = $schedule->status;
        $trip->cancellation_policy_id = $schedule->cancellation_policy_id;
        $trip->vehicle_id = $schedule->vehicle_id;
        $trip->amenities = $schedule->amenities;

        $trip->save();

        // Handle amenities (legacy sync)
        if ($schedule->amenities && is_array($schedule->amenities)) {
            \App\Models\TripAmenity::where('trip_id', $trip->id)->delete();
            foreach ($schedule->amenities as $amenityId) {
                $template = \App\Models\AmenityTemplate::find($amenityId);
                if($template) {
                    \App\Models\TripAmenity::create([
                        'trip_id' => $trip->id,
                        'amenity' => $template->key ?? $template->label,
                    ]);
                }
            }
        }

        // Phase 1.2: Copy boarding points from schedule to trip
        foreach ($schedule->scheduleBoardingPoints as $schedulePoint) {
            $scheduledTime = $this->calculateTimeFromOffset($schedule->starts_from, $schedulePoint->time_offset_minutes);
            
            \App\Models\TripBoardingPoint::create([
                'trip_id' => $trip->id,
                'boarding_point_id' => $schedulePoint->boarding_point_id,
                'scheduled_time' => $scheduledTime,
                'sort_order' => $schedulePoint->sort_order,
                'notes' => $schedulePoint->notes,
                'passenger_count' => 0,
            ]);
        }

        // Phase 1.2: Copy dropping points from schedule to trip
        foreach ($schedule->scheduleDroppingPoints as $schedulePoint) {
            $scheduledTime = $this->calculateTimeFromOffset($schedule->starts_from, $schedulePoint->time_offset_minutes);
            
            \App\Models\TripDroppingPoint::create([
                'trip_id' => $trip->id,
                'dropping_point_id' => $schedulePoint->dropping_point_id,
                'scheduled_time' => $scheduledTime,
                'sort_order' => $schedulePoint->sort_order,
                'notes' => $schedulePoint->notes,
                'passenger_count' => 0,
            ]);
        }

        return true;
    }

    protected function generateTitle(Schedule $schedule, Carbon $date)
    {
        $routeName = $schedule->route ? $schedule->route->name : 'Unknown Route';
        return $schedule->name ?: "{$routeName} - " . $date->format('Y-m-d');
    }

    /**
     * Calculate actual time from base time and offset in minutes.
     * Phase 1.2: Helper for point time calculation.
     */
    protected function calculateTimeFromOffset($baseTime, $offsetMinutes)
    {
        $time = Carbon::parse($baseTime);
        return $time->addMinutes($offsetMinutes)->format('H:i:s');
    }
}
