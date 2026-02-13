<?php

namespace App\Traits;

use App\Models\CoOwner;
use App\Models\CounterManager;
use App\Models\Driver;
use App\Models\Supervisor;

trait HasPermissions
{
    public function hasPermission($permission)
    {
        $permissions = $this->permissions;

        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true);
        }

        if ($permissions && is_array($permissions) && in_array($permission, $permissions)) {
            return true;
        }

        // Default permissions if no override is set
        return $this->getDefaultPermissions($permission);
    }

    protected function getDefaultPermissions($permission)
    {
        $role = $this->getRoleName();

        $defaults = [
            'co-owner' => [
                'fleet_management',
                'staff_management',
                'trip_management',
                'ticket_management',
                'booking_management',
                'sales_reports',
                'boarding_management'
            ],
            'manager' => [
                'booking_management',
                'sales_reports'
            ],
            'supervisor' => [
                'boarding_management',
                'trip_management'
            ],
            'driver' => [
                'trip_management'
            ]
        ];

        return isset($defaults[$role]) && in_array($permission, $defaults[$role]);
    }

    protected function getRoleName()
    {
        if ($this instanceof CoOwner) return 'co-owner';
        if ($this instanceof CounterManager) return 'manager';
        if ($this instanceof Supervisor) return 'supervisor';
        if ($this instanceof Driver) return 'driver';

        return null;
    }
}
