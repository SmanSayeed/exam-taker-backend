<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run()
    {
        $subjects = DB::table('subjects')->pluck('id')->toArray();

        $lessons = [];

        foreach ($subjects as $subjectId) {
            for ($i = 1; $i <= 10; $i++) {
                $lessons[] = [
                    'subject_id' => $subjectId,
                    'title' => 'Lesson ' . $i . ' for Subject ' . $subjectId,
                    'details' => 'Details for Lesson ' . $i,
                    'image' => 'lesson' . $i . '.jpg',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($lessons) >= 200) break 2;
            }
        }

        DB::table('lessons')->insert($lessons);
    }
}
