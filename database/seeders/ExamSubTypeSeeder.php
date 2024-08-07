<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSubTypeSeeder extends Seeder
{
    public function run()
    {
        $examTypes = DB::table('exam_types')->pluck('id');
        $examSubTypes = [];

        foreach ($examTypes as $examTypeId) {
            for ($i = 1; $i <= 3; $i++) {  // Generate 3 exam sub-types per exam type
                $examSubTypes[] = [
                    'exam_type_id' => $examTypeId,
                    'title' => 'Exam Sub Type ' . $i . ' for Exam Type ' . $examTypeId,
                    'details' => 'Details for Exam Sub Type ' . $i,
                    'image' => 'exam_sub_type' . $i . '.jpg',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('exam_sub_types')->insert($examSubTypes);
    }
}
