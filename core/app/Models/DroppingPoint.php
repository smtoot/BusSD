<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class DroppingPoint extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'owner_id',
        'city_id',
        'name',
        'landmark',
        'description',
        'address',
        'latitude',
        'longitude',
        'contact_phone',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Dropping Point Types
     */
    const TYPE_BUS_STAND = 'bus_stand';
    const TYPE_CITY_CENTER = 'city_center';
    const TYPE_AIRPORT = 'airport';
    const TYPE_CUSTOM = 'custom';

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_dropping_points')
            ->withPivot('dropoff_time_offset', 'sort_order')
            ->orderBy('pivot_sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            self::TYPE_BUS_STAND => 'Bus Stand',
            self::TYPE_CITY_CENTER => 'City Center',
            self::TYPE_AIRPORT => 'Airport',
            self::TYPE_CUSTOM => 'Custom',
        ];
        return $labels[$this->type] ?? 'Unknown';
    }

    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->name) $parts[] = $this->name;
        if ($this->landmark) $parts[] = $this->landmark;
        if ($this->address) $parts[] = $this->address;
        return implode(', ', $parts);
    }

    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }
        return null;
    }
}
