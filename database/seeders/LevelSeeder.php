<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    public function run()
    {
        // Retrieve the IDs of the groups
        $groupIds = DB::table('groups')->pluck('id')->toArray();

        $levels = [];
        $levelTitles = ['HSC', 'Class 9', 'Class 10', 'Class 11', 'Class 12'];

        foreach ($groupIds as $groupId) {
            foreach ($levelTitles as $index => $title) {
                $uniqueTitle = $title . ' ' . ($index + 1) . ' for Group ' . $groupId;
                $levels[] = [
                    'group_id' => $groupId,
                    'title' => $uniqueTitle,
                    'details' => 'Details for ' . $uniqueTitle,
                    'image' => strtolower(str_replace(' ', '_', $uniqueTitle)) . '.jpg',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('levels')->insert($levels);
    }
}
