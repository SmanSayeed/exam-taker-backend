<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PdfSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pdf_subscriptions')->insert([
            [
                'student_id' => 1,
                'pdf_id' => 1,
                'subscribed_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addDays(25),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'student_id' => 1,
                'pdf_id' => 2,
                'subscribed_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addDays(20),
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'student_id' => 1,
                'pdf_id' => 1,
                'subscribed_at' => Carbon::now()->subDays(15),
                'expires_at' => Carbon::now()->subDays(1), // Expired subscription
                'is_active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
