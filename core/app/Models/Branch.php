<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use GlobalStatus;

    protected $table = 'branches';

    protected $fillable = [
        'owner_id',
        'name',
        'code',
        'type',
        'city_id',
        'location',
        'mobile',
        'contact_email',
        'autonomy_level',
        'can_set_routes',
        'can_adjust_pricing',
        'pricing_variance_limit',
        'allows_online_booking',
        'allows_counter_booking',
        'timezone',
        'tax_registration_no',
        'bank_account_details',
        'counter_manager_id',
        'status'
    ];

    protected $casts = [
        'can_set_routes' => 'boolean',
        'can_adjust_pricing' => 'boolean',
        'allows_online_booking' => 'boolean',
        'allows_counter_booking' => 'boolean',
        'bank_account_details' => 'array',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function manager()
    {
        return $this->belongsTo(CounterManager::class, 'counter_manager_id');
    }

    public function counterManager()
    {
        return $this->belongsTo(CounterManager::class, 'counter_manager_id');
    }

    public function staff()
    {
        return $this->hasMany(BranchStaff::class);
    }

    public function operatingHours()
    {
        return $this->hasMany(BranchOperatingHours::class);
    }

    public function primaryVehicles()
    {
        return $this->hasMany(Vehicle::class, 'primary_branch_id');
    }

    public function currentVehicles()
    {
        return $this->hasMany(Vehicle::class, 'current_branch_id');
    }

    public function ownedTrips()
    {
        return $this->hasMany(Trip::class, 'owning_branch_id');
    }

    public function revenues()
    {
        return $this->hasMany(BranchRevenue::class);
    }

    // Helper Methods
    public function isAutonomous()
    {
        return $this->autonomy_level === 'autonomous';
    }

    public function isSemiAutonomous()
    {
        return $this->autonomy_level === 'semi_autonomous';
    }

    public function isControlled()
    {
        return $this->autonomy_level === 'controlled';
    }

    public function canManagePricing()
    {
        return $this->can_adjust_pricing;
    }

    public function canManageRoutes()
    {
        return $this->can_set_routes;
    }

    public function generateCode()
    {
        if ($this->code) {
            return $this->code;
        }

        $cityCode = strtoupper(substr($this->city->name ?? 'BRN', 0, 3));
        $sequence = static::where('owner_id', $this->owner_id)
            ->where('city_id', $this->city_id)
            ->count() + 1;
        
        return $cityCode . '-' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($branch) {
            if (!$branch->code && $branch->city_id) {
                $branch->code = $branch->generateCode();
            }
        });
    }
}
