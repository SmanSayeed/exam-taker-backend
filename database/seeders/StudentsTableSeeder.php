<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('students')->insert([
            'name' => 'Student One', // You can change this to any name
            'email' => 's1@gmail.com',
            'phone' => '0123456789', // You can set this as needed
            'email_verified_at' => Carbon::now(),
            'section_id' => 1,
            'phone_verified_at' => Carbon::now(),
            'password' => bcrypt('11112222'), // The password is hashed using bcrypt
            'profile_image' => null, // No profile image for now
            'ip_address' => '192.168.1.1', // Example IP address, you can set this as needed
            'country' => 'Bangladesh', // Example country
            'country_code' => 'BD', // Example country code
            'address' => '123 Some Street, Dhaka', // Example address
            'active_status' => true, // Set the active status as true
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
