<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamTypeSeeder extends Seeder
{
    public function run()
    {
        $sections = DB::table('sections')->pluck('id');
        $examTypes = [];

        foreach ($sections as $sectionId) {
            for ($i = 1; $i <= 5; $i++) {  // Generate 5 exam types per section
                $examTypes[] = [
                    'section_id' => $sectionId,
                    'title' => 'Exam Type ' . $i . ' for Section ' . $sectionId,
                    'details' => 'Details for Exam Type ' . $i,
                    'image' => 'exam_type' . $i . '.jpg',
                    'status' => true,
                    'year' => '2024',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('exam_types')->insert($examTypes);
    }
}
