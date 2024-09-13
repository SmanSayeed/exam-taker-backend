<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Question;
use App\Models\McqQuestion;
use App\Models\CreativeQuestion;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the questions from the JSON file
        $json = File::get("database/data/questions.json");
        $questions = json_decode($json, true);

        // Loop through each question and create the appropriate records
        foreach ($questions as $questionData) {
            $question = Question::create([
                'title' => $questionData['title'],
                'description' => $questionData['description'],
                'images' => $questionData['images'],
                'is_paid' => $questionData['is_paid'],
                'is_featured' => $questionData['is_featured'],
                'type' => $questionData['type'],
                'status' => $questionData['status'],
                'mark' => $questionData['mark'],
            ]);

            // If it's an MCQ type, create the MCQ options
            if ($questionData['type'] === 'mcq') {
                $i=1;
                foreach ($questionData['mcq_options'] as $option) {
                    McqQuestion::create([
                        'question_id' => $question->id,
                        'mcq_question_text' => $option['option_text'],
                        'mcq_option_serial' => $option['mcq_option_serial']??$i++,
                        'is_correct' => $option['is_correct'],
                        'description' => $questionData['description'],
                    ]);
                }
            }

            // If it's a Creative type, handle the creative options
            if ($questionData['type'] === 'creative') {
                $creativeOptions = $questionData['creative_options'] ?? [];
                foreach (['a', 'b', 'c', 'd'] as $type) {
                    CreativeQuestion::create([
                        'question_id' => $question->id,
                        'creative_question_text' => $questionData['title'],
                        'creative_question_type' => $type,
                        'description' => $creativeOptions[$type] ?? '', // Default to empty string if not set
                    ]);
                }
            }
        }
    }
}
