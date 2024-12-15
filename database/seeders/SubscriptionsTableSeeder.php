<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $index) {
            DB::table('subscriptions')->insert([
                'student_id' => 1,
                'package_id' => 1,
                'expires_at' => Carbon::now()->addMonths(rand(1, 6))->toDateString(),
                'is_active' => rand(0, 1) == 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
