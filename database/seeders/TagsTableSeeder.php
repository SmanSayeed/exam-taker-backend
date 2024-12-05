<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach (range(1, 10) as $index) {
            DB::table('tags')->insert([
                'title' => 'Tag ' . $index,
                'status' => rand(0, 1) == 1,
                'details' => 'This is the details for tag ' . $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
