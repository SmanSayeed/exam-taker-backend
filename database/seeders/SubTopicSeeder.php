<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubTopicSeeder extends Seeder
{
    public function run()
    {
        $topics = DB::table('topics')->pluck('id')->toArray();

        $subTopics = [];

        foreach ($topics as $topicId) {
            for ($i = 1; $i <= 10; $i++) {
                $subTopics[] = [
                    'topic_id' => $topicId,
                    'title' => 'SubTopic ' . $i . ' for Topic ' . $topicId,
                    'details' => 'Details for SubTopic ' . $i,
                    'image' => 'subtopic' . $i . '.jpg',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($subTopics) >= 200) break 2;
            }
        }

        DB::table('sub_topics')->insert($subTopics);
    }
}
