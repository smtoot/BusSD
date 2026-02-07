<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Supervisor;
use App\Constants\Status;

class DemoAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Admin
        $admin = Admin::where('username', 'admin')->first();
        if (!$admin) {
            $admin = new Admin();
            $admin->name = 'Super Admin';
            $admin->email = 'admin@example.com'; 
            $admin->username = 'admin';
            $admin->password = Hash::make('admin');
            $admin->save();
            echo "Admin created: admin / admin\n";
        } else {
            echo "Admin already exists.\n";
        }

        // 2. Create Owner (Operator)
        $owner = Owner::where('username', 'operator')->first();
        if (!$owner) {
            $owner = new Owner();
            $owner->firstname = 'Demo';
            $owner->lastname = 'Operator';
            $owner->username = 'operator';
            $owner->email = 'operator@example.com';
            $owner->country_code = '1';
            $owner->mobile = '5551234567';
            $owner->password = Hash::make('operator');
            $owner->status = Status::USER_ACTIVE;
            $owner->ev = Status::VERIFIED; // Email Verified
            $owner->sv = Status::VERIFIED; // SMS Verified
            $owner->save();
            echo "Operator created: operator / operator\n";
        } else {
            echo "Operator already exists.\n";
        }
        
        // 3. Create Supervisor
        if ($owner) {
            $supervisor = Supervisor::where('username', 'supervisor')->first();
            if (!$supervisor) {
                $supervisor = new Supervisor();
                $supervisor->owner_id = $owner->id;
                $supervisor->firstname = 'Demo';
                $supervisor->lastname = 'Supervisor';
                $supervisor->username = 'supervisor';
                $supervisor->email = 'supervisor@example.com';
                $supervisor->password = Hash::make('supervisor');
                $supervisor->mobile = '5559876543';
                $supervisor->status = Status::ENABLE; // Assuming GlobalStatus Enable = 1
                $supervisor->save();
                echo "Supervisor created: supervisor / supervisor\n";
            } else {
                echo "Supervisor already exists.\n";
            }
        }
    }
}
