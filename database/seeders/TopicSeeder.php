<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    public function run()
    {
        $lessons = DB::table('lessons')->pluck('id')->toArray();

        $topics = [];

        foreach ($lessons as $lessonId) {
            for ($i = 1; $i <= 10; $i++) {
                $topics[] = [
                    'lesson_id' => $lessonId,
                    'title' => 'Topic ' . $i . ' for Lesson ' . $lessonId,
                    'description' => 'Description for Topic ' . $i,
                    'image' => 'topic' . $i . '.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($topics) >= 200) break 2;
            }
        }

        DB::table('topics')->insert($topics);
    }
}
