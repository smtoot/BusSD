<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchStaff extends Model
{
    protected $table = 'branch_staff';

    protected $fillable = [
        'branch_id',
        'user_id',
        'role',
        'permissions',
        'is_active',
        'assigned_date'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'assigned_date' => 'date'
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(CounterManager::class, 'user_id');
    }

    public function counterManager()
    {
        return $this->belongsTo(CounterManager::class, 'user_id');
    }

    // Helper Methods
    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }
}
