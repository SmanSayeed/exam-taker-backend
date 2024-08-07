<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SectionSeeder extends Seeder
{
    public function run()
    {
        $sections = [];

        for ($i = 1; $i <= 200; $i++) {
            $sections[] = [
                'title' => 'Section ' . $i,
                'details' => 'Details for Section ' . $i,
                'image' => 'image' . $i . '.jpg',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('sections')->insert($sections);
    }
}
