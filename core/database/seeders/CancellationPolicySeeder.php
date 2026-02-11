<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CancellationPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = [
            [
                'name' => 'flexible',
                'label' => 'Flexible',
                'description' => 'Full refund up to 24 hours before departure',
                'rules' => json_encode([
                    ['hours_before' => 24, 'refund_percentage' => 100],
                    ['hours_before' => 0, 'refund_percentage' => 0],
                ]),
                'is_default' => true,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'moderate',
                'label' => 'Moderate',
                'description' => 'Full refund up to 48 hours, 50% refund up to 24 hours before departure',
                'rules' => json_encode([
                    ['hours_before' => 48, 'refund_percentage' => 100],
                    ['hours_before' => 24, 'refund_percentage' => 50],
                    ['hours_before' => 0, 'refund_percentage' => 0],
                ]),
                'is_default' => false,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'strict',
                'label' => 'Strict',
                'description' => 'Full refund up to 7 days, 50% refund up to 48 hours before departure',
                'rules' => json_encode([
                    ['hours_before' => 168, 'refund_percentage' => 100], // 7 days = 168 hours
                    ['hours_before' => 48, 'refund_percentage' => 50],
                    ['hours_before' => 0, 'refund_percentage' => 0],
                ]),
                'is_default' => false,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($policies as $policy) {
            DB::table('cancellation_policies')->insert($policy);
        }
    }
}
