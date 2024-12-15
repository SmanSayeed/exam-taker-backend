<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentPaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $index) {
            DB::table('student_payments')->insert([
                'student_id' => 1,
                'package_id' => 1,
                'payment_method' => collect(['bkash', 'nagad', 'rocket'])->random(), // Random payment method
                'mobile_number' => '01' . rand(10000000, 99999999),
                'transaction_id' => 'TXN' . rand(100000000, 999999999),
                'amount' => rand(100, 1000),  // Random amount between 100 and 1000
                'coupon' => collect(['COUPON1', 'COUPON2', 'COUPON3'])->random(),
                'verified' => rand(0, 1) == 1,
                'verified_at' => rand(0, 1) == 1 ? Carbon::now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
