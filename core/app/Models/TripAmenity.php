<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class TripAmenity extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'trip_id',
        'amenity',
    ];

    /**
     * Get the trip that owns the amenity.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Available amenity options
     */
    public static function getAvailableAmenities()
    {
        return [
            'wifi' => [
                'label' => 'WiFi',
                'icon' => 'fa-wifi',
                'category' => 'connectivity'
            ],
            'charging_ports' => [
                'label' => 'Charging Ports',
                'icon' => 'fa-plug',
                'category' => 'connectivity'
            ],
            'ac' => [
                'label' => 'Air Conditioning',
                'icon' => 'fa-snowflake',
                'category' => 'comfort'
            ],
            'water' => [
                'label' => 'Water Bottle',
                'icon' => 'fa-tint',
                'category' => 'comfort'
            ],
            'blanket' => [
                'label' => 'Blanket',
                'icon' => 'fa-bed',
                'category' => 'comfort'
            ],
            'pillow' => [
                'label' => 'Pillow',
                'icon' => 'fa-couch',
                'category' => 'comfort'
            ],
            'reading_light' => [
                'label' => 'Reading Light',
                'icon' => 'fa-lightbulb',
                'category' => 'comfort'
            ],
            'tv' => [
                'label' => 'TV/Entertainment',
                'icon' => 'fa-tv',
                'category' => 'entertainment'
            ],
            'toilet' => [
                'label' => 'Toilet',
                'icon' => 'fa-restroom',
                'category' => 'facilities'
            ],
            'emergency_exit' => [
                'label' => 'Emergency Exit',
                'icon' => 'fa-door-open',
                'category' => 'safety'
            ],
            'gps_tracking' => [
                'label' => 'GPS Tracking',
                'icon' => 'fa-map-marker-alt',
                'category' => 'safety'
            ],
            'cctv' => [
                'label' => 'CCTV Cameras',
                'icon' => 'fa-video',
                'category' => 'safety'
            ],
            'first_aid' => [
                'label' => 'First Aid Kit',
                'icon' => 'fa-medkit',
                'category' => 'safety'
            ],
            'fire_extinguisher' => [
                'label' => 'Fire Extinguisher',
                'icon' => 'fa-fire-extinguisher',
                'category' => 'safety'
            ],
            'usb_charging' => [
                'label' => 'USB Charging',
                'icon' => 'fa-usb',
                'category' => 'connectivity'
            ],
            'meals' => [
                'label' => 'Meals/Snacks',
                'icon' => 'fa-utensils',
                'category' => 'comfort'
            ],
            'magazines' => [
                'label' => 'Magazines/Newspapers',
                'icon' => 'fa-newspaper',
                'category' => 'entertainment'
            ],
            'reclining_seats' => [
                'label' => 'Reclining Seats',
                'icon' => 'fa-chair',
                'category' => 'comfort'
            ],
            'foot_rest' => [
                'label' => 'Foot Rest',
                'icon' => 'fa-shoe-prints',
                'category' => 'comfort'
            ],
        ];
    }

    /**
     * Get amenity label
     */
    public function getLabelAttribute()
    {
        $amenities = self::getAvailableAmenities();
        return $amenities[$this->amenity]['label'] ?? $this->amenity;
    }

    /**
     * Get amenity icon
     */
    public function getIconAttribute()
    {
        $amenities = self::getAvailableAmenities();
        return $amenities[$this->amenity]['icon'] ?? 'fa-circle';
    }

    /**
     * Get amenity category
     */
    public function getCategoryAttribute()
    {
        $amenities = self::getAvailableAmenities();
        return $amenities[$this->amenity]['category'] ?? 'other';
    }
}
