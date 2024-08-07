<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $groups = [];

        $groupTitles = ['Science', 'Commerce', 'Arts'];

        foreach ($groupTitles as $title) {
            $groups[] = [
                'title' => $title,
                'details' => 'Details for ' . $title,
                'image' => strtolower($title) . '.jpg',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('groups')->insert($groups);
    }
}
