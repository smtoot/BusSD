<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Owner;
use App\Models\FleetType;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Counter;
use App\Models\StopPoint;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $owner = Owner::where('username', 'operator')->first();
        
        if (!$owner) {
            $this->command->error("Operator not found! Run DemoAccountSeeder first.");
            return;
        }

        // 1. Seat Layout
        $layout = \App\Models\SeatLayout::firstOrCreate([
            'owner_id' => $owner->id,
            'name' => '2x2 Standard',
        ], [
            'layout' => '2x2',
            'schema' => json_decode('{"meta":{"rows":10,"cols":5},"layout":[]}'),
            'status' => 1,
        ]);
        $this->command->info("Seat Layout seeded: {$layout->name}");

        // 2. Fleet Type
        $fleetType = FleetType::firstOrCreate([
            'owner_id' => $owner->id,
            'name' => 'Volvo Multi-Axle AC',
        ], [
            'seat_layout_id' => $layout->id,
            'deck' => 1,
            'seats' => ['A1', 'A2', 'B1', 'B2'], // Dummy seats
            'status' => 1,
            'has_ac' => 1,
        ]);
        $this->command->info("Fleet Type seeded: {$fleetType->name}");

        // 3. Vehicle
        $vehicle = Vehicle::firstOrCreate([
            'owner_id' => $owner->id,
            'registration_no' => 'KRT-1234',
        ], [
            'fleet_type_id' => $fleetType->id,
            'nick_name' => 'Blue Bird',
            'engine_no' => 'ENG-98765',
            'chasis_no' => 'CHS-54321',
            'model_no' => '2025',
            'status' => 1,
        ]);
        $this->command->info("Vehicle seeded: {$vehicle->registration_no}");

        // 3. Stop Points (Counters)
        $khartoum = Counter::firstOrCreate(['name' => 'Khartoum Central', 'owner_id' => $owner->id], [
            'city' => 'Khartoum',
            'location' => 'Central Station',
            'mobile' => '0912345678',
            'status' => 1,
        ]);
        
        $portSudan = Counter::firstOrCreate(['name' => 'Port Sudan Terminal', 'owner_id' => $owner->id], [
            'city' => 'Port Sudan',
            'location' => 'Main Port',
            'mobile' => '0987654321',
            'status' => 1,
        ]);

        // 4. Route
        $route = Route::firstOrCreate([
            'owner_id' => $owner->id,
            'name' => 'Khartoum - Port Sudan Express',
        ], [
            'starting_point' => $khartoum->id,
            'destination_point' => $portSudan->id,
            'distance' => '800 km',
            'time' => '10 Hours',
            'status' => 1,
        ]);
        $this->command->info("Route seeded: {$route->name}");
    }
}
