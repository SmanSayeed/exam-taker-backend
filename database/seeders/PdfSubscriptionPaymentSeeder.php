<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PdfSubscriptionPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pdf_subscription_payments')->insert([
            [
                'student_id' => 1,
                'pdf_subscription_id' => 1,
                'pdf_id' => 2,
                'payment_method' => 'bkash',
                'mobile_number' => '01700000000',
                'transaction_id' => 'TRX123456789',
                'amount' => 500.00,
                'coupon' => 'DISCOUNT50',
                'verified' => true,
                'verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'student_id' => 1,
                'pdf_subscription_id' => 2,
                'pdf_id' => 2,
                'payment_method' => 'nagad',
                'mobile_number' => '01800000000',
                'transaction_id' => 'TRX987654321',
                'amount' => 450.00,
                'coupon' => null,
                'verified' => false,
                'verified_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'student_id' => 1,
                'pdf_subscription_id' => 3,
                'pdf_id' => 3,
                'payment_method' => 'rocket',
                'mobile_number' => '01900000000',
                'transaction_id' => 'TRX1122334455',
                'amount' => 600.00,
                'coupon' => 'WELCOME10',
                'verified' => true,
                'verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
