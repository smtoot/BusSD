<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class AmenityTemplate extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'key',
        'label',
        'icon',
        'category',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get amenities grouped by category
     */
    public static function getByCategory()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get active amenities only
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Category labels
     */
    public static function getCategoryLabels()
    {
        return [
            'connectivity' => 'Connectivity',
            'comfort' => 'Comfort',
            'entertainment' => 'Entertainment',
            'facilities' => 'Facilities',
            'safety' => 'Safety',
            'other' => 'Other',
        ];
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        $labels = self::getCategoryLabels();
        return $labels[$this->category] ?? $this->category;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Overriding GlobalStatus Trait)
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
