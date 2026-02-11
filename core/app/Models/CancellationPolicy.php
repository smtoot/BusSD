<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'name',
        'label',
        'description',
        'rules',
        'is_default',
        'is_system',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rules' => 'array',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get trips using this policy
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get refund percentage for given hours before departure
     */
    public function getRefundPercentage($hoursBeforeDeparture)
    {
        if (!$this->rules || count($this->rules) == 0) {
            return 0;
        }

        // Sort rules by hours_before descending
        $sortedRules = collect($this->rules)->sortByDesc('hours_before');

        foreach ($sortedRules as $rule) {
            if ($hoursBeforeDeparture >= $rule['hours_before']) {
                return $rule['refund_percentage'];
            }
        }

        return 0;
    }

    /**
     * Get formatted rules for display
     */
    public function getFormattedRulesAttribute()
    {
        if (!$this->rules) {
            return [];
        }

        return collect($this->rules)->map(function ($rule) {
            $hours = $rule['hours_before'];
            $days = floor($hours / 24);
            $remainingHours = $hours % 24;

            $timeStr = '';
            if ($days > 0) {
                $timeStr = $days . ' ' . trans_choice('days', $days);
                if ($remainingHours > 0) {
                    $timeStr .= ' ' . $remainingHours . ' ' . trans_choice('hours', $remainingHours);
                }
            } else {
                $timeStr = $hours . ' ' . trans_choice('hours', $hours);
            }

            return [
                'time' => $timeStr,
                'refund' => $rule['refund_percentage'] . '%',
                'hours' => $hours,
                'percentage' => $rule['refund_percentage'],
            ];

        })->sortByDesc('hours')->values()->all();
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
