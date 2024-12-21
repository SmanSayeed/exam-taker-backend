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
            [
                'name' => 'Student One',
                'email' => 's1@gmail.com',
                'phone' => '0123456789',
                'email_verified_at' => Carbon::now(),
                'section_id' => 1,
                'phone_verified_at' => Carbon::now(),
                'password' => bcrypt('11112222'),
                'profile_image' => null,
                'ip_address' => '192.168.1.1',
                'country' => 'Bangladesh',
                'country_code' => 'BD',
                'address' => '123 Some Street, Dhaka',
                'active_status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Student Two', // New student name
                'email' => 's2@gmail.com',
                'phone' => '0987654321',
                'email_verified_at' => Carbon::now(),
                'section_id' => 1,
                'phone_verified_at' => Carbon::now(),
                'password' => bcrypt('22334455'),
                'profile_image' => null,
                'ip_address' => '192.168.1.2', // New IP address
                'country' => 'Bangladesh',
                'country_code' => 'BD',
                'address' => '456 Another Street, Dhaka',
                'active_status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
