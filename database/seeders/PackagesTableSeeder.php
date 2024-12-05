<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('packages')->insert([
                'name' => 'Package ' . $i,
                'description' => 'This is package number ' . $i,
                'is_active' => true,
                'duration_days' => rand(30, 365), // Random duration between 30 and 365 days
                'price' => rand(20, 100), // Random price between 20 and 100
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
