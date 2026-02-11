<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmenityTemplate;

class AmenityCategorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Categorize amenities as 'vehicle' (built-in hardware) or 'trip' (service options)
     */
    public function run(): void
    {
        // Vehicle-level amenities (built-in hardware features)
        $vehicleAmenities = [
            'ac', 'wifi', 'usb_charging', 'charging_ports', 'toilet', 'tv',
            'reclining_seats', 'foot_rest', 'reading_light', 'cctv',
            'gps_tracking', 'emergency_exit', 'fire_extinguisher', 'first_aid'
        ];

        // Trip-level amenities (service options)
        $tripAmenities = [
            'water', 'blanket', 'pillow', 'meals', 'magazines'
        ];

        // Update vehicle amenities
        AmenityTemplate::whereIn('key', $vehicleAmenities)
            ->update(['amenity_type' => 'vehicle']);

        // Update trip amenities
        AmenityTemplate::whereIn('key', $tripAmenities)
            ->update(['amenity_type' => 'trip']);

        $this->command->info('Amenity categorization completed successfully!');
        $this->command->info('Vehicle amenities: ' . implode(', ', $vehicleAmenities));
        $this->command->info('Trip amenities: ' . implode(', ', $tripAmenities));
    }
}
