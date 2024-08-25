<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $levels = DB::table('levels')->pluck('id')->toArray();
        $groups = DB::table('groups')->pluck('id')->toArray();

        $subjects = [];

        foreach ($levels as $levelId) {
            foreach ($groups as $groupId) {
                for ($i = 1; $i <= 10; $i++) {
                    $subjects[] = [
                        'level_id' => $levelId,
                        'group_id' => $groupId,
                        'part' => $i % 2 == 0 ? '2' : '1',
                        'title' => 'Subject ' . $i . ' for Level ' . $levelId . ' and Group ' . $groupId,
                        'details' => 'Details for Subject ' . $i,
                        'image' => 'subject' . $i . '.jpg',
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($subjects) >= 200) break 2;
                }
            }
        }

        DB::table('subjects')->insert($subjects);
    }
}
