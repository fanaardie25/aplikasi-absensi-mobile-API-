<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Setting::create([
            'key' => 'school_latitude',
            'value' => '-7.390022513649234', 
        ]);

        Setting::create([
            'key' => 'school_longitude',
            'value' => '110.51808635390792',
        ]);

        Setting::create([
            'key' => 'attendance_radius',
            'value' => '100', 
        ]);

        Setting::create([
            'key' => 'start_time',
            'value' => '12:00:00',
        ]);
        
    }
}