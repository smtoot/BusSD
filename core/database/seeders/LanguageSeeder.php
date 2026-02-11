<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Constants\Status;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $language = Language::firstOrCreate([
            'code' => 'ar'
        ], [
            'name' => 'Arabic',
            'is_default' => Status::DISABLE,
            'is_rtl'     => Status::ENABLE, // Assuming is_rtl exists from migration
            'status'     => Status::ENABLE, // Default status likely just 'status' or via boot
        ]);

        if ($language->wasRecentlyCreated) {
            $this->command->info('Arabic language added successfully!');
        } else {
            $this->command->info('Arabic language already exists.');
        }
    }
}
