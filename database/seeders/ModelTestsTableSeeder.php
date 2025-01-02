<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelTestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Loop to insert 10 model tests
        for ($i = 1; $i <= 10; $i++) {
            DB::table('model_tests')->insert([
                'package_id' => rand(1, 10), // Randomly associate with one of the 10 packages
                'title' => 'Model Test ' . $i,
                'description' => 'This is a description for model test ' . $i,
                'start_time' => now()->addDays(rand(1, 7)), // Random start time within the next 7 days
                'end_time' => now()->addDays(rand(1, 7))->addHours(rand(1, 2)), // Random end time (1 to 2 hours after start time)
                'is_active' => true,
                'full_mark' => rand(100, 200),
                'pass_mark' => rand(50, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
