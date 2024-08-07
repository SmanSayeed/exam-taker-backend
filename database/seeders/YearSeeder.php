<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearSeeder extends Seeder
{
    public function run()
    {
        $years = [];

        for ($i = 1990; $i <= 2024; $i++) {
            $years[] = [
                'title' =>  $i,
                'details' => 'Details for Year ' . $i,
                'image' => 'year' . $i . '.jpg',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('years')->insert($years);
    }
}
