<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenityTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            // Connectivity
            ['key' => 'wifi', 'label' => 'WiFi', 'icon' => 'fa-wifi', 'category' => 'connectivity', 'is_system' => true, 'sort_order' => 1],
            ['key' => 'charging_ports', 'label' => 'Charging Ports', 'icon' => 'fa-plug', 'category' => 'connectivity', 'is_system' => true, 'sort_order' => 2],
            ['key' => 'usb_charging', 'label' => 'USB Charging', 'icon' => 'fa-usb', 'category' => 'connectivity', 'is_system' => true, 'sort_order' => 3],
            
            // Comfort
            ['key' => 'ac', 'label' => 'Air Conditioning', 'icon' => 'fa-snowflake', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 4],
            ['key' => 'water', 'label' => 'Water Bottle', 'icon' => 'fa-tint', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 5],
            ['key' => 'blanket', 'label' => 'Blanket', 'icon' => 'fa-bed', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 6],
            ['key' => 'pillow', 'label' => 'Pillow', 'icon' => 'fa-couch', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 7],
            ['key' => 'reading_light', 'label' => 'Reading Light', 'icon' => 'fa-lightbulb', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 8],
            ['key' => 'meals', 'label' => 'Meals/Snacks', 'icon' => 'fa-utensils', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 9],
            ['key' => 'reclining_seats', 'label' => 'Reclining Seats', 'icon' => 'fa-chair', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 10],
            ['key' => 'foot_rest', 'label' => 'Foot Rest', 'icon' => 'fa-shoe-prints', 'category' => 'comfort', 'is_system' => true, 'sort_order' => 11],
            
            // Entertainment
            ['key' => 'tv', 'label' => 'TV/Entertainment', 'icon' => 'fa-tv', 'category' => 'entertainment', 'is_system' => true, 'sort_order' => 12],
            ['key' => 'magazines', 'label' => 'Magazines/Newspapers', 'icon' => 'fa-newspaper', 'category' => 'entertainment', 'is_system' => true, 'sort_order' => 13],
            
            // Facilities
            ['key' => 'toilet', 'label' => 'Toilet', 'icon' => 'fa-restroom', 'category' => 'facilities', 'is_system' => true, 'sort_order' => 14],
            
            // Safety
            ['key' => 'emergency_exit', 'label' => 'Emergency Exit', 'icon' => 'fa-door-open', 'category' => 'safety', 'is_system' => true, 'sort_order' => 15],
            ['key' => 'gps_tracking', 'label' => 'GPS Tracking', 'icon' => 'fa-map-marker-alt', 'category' => 'safety', 'is_system' => true, 'sort_order' => 16],
            ['key' => 'cctv', 'label' => 'CCTV Cameras', 'icon' => 'fa-video', 'category' => 'safety', 'is_system' => true, 'sort_order' => 17],
            ['key' => 'first_aid', 'label' => 'First Aid Kit', 'icon' => 'fa-medkit', 'category' => 'safety', 'is_system' => true, 'sort_order' => 18],
            ['key' => 'fire_extinguisher', 'label' => 'Fire Extinguisher', 'icon' => 'fa-fire-extinguisher', 'category' => 'safety', 'is_system' => true, 'sort_order' => 19],
        ];

        foreach ($amenities as $amenity) {
            DB::table('amenity_templates')->insert(array_merge($amenity, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
