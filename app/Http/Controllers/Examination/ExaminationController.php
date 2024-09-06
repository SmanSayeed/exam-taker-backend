<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Models\Examination;
use App\Models\Question;
use Carbon\Carbon;
use App\Http\Requests\StartExamRequest;
use Illuminate\Support\Facades\DB;
class ExaminationController extends Controller
{
    public function startExam(StartExamRequest $request)
    {
        $validatedData = $request->validated();

        $questionsLimit = $validatedData['questions_limit'] ?? 20; // Default to 20 questions if not provided

        // Parse category-related IDs from request and store them as comma-separated strings
        $categories = [
            'section' => !empty($validatedData['section_categories']) ? implode(',', $validatedData['section_categories']) : null,
            'exam_type' => !empty($validatedData['exam_type_categories']) ? implode(',', $validatedData['exam_type_categories']) : null,
            'exam_sub_type' => !empty($validatedData['exam_sub_type_categories']) ? implode(',', $validatedData['exam_sub_type_categories']) : null,
            'group' => !empty($validatedData['group_categories']) ? implode(',', $validatedData['group_categories']) : null,
            'level' => !empty($validatedData['level_categories']) ? implode(',', $validatedData['level_categories']) : null,
            'lesson' => !empty($validatedData['lesson_categories']) ? implode(',', $validatedData['lesson_categories']) : null,
            'topic' => !empty($validatedData['topic_categories']) ? implode(',', $validatedData['topic_categories']) : null,
            'sub_topic' => !empty($validatedData['sub_topic_categories']) ? implode(',', $validatedData['sub_topic_categories']) : null,
        ];

        // Get the list of questions based on filtering
        $query = Question::where('type', $validatedData['question_type']);

        $query = $this->filterQuestionsByCategories($query, $categories);

        // Randomly select the specified number of questions
        $questions = $query->inRandomOrder()->limit($questionsLimit)->pluck('id')->toArray();

        // Use a transaction to ensure both operations succeed or fail together
        DB::beginTransaction();

        try {
            // Create the exam
            $exam = Examination::create([
                'title' => $validatedData['title'],
                'description' => $request->input('description', null),
                'type' => $validatedData['type'],
                'is_paid' => $request->input('is_paid', false),
                'created_by' => $validatedData['created_by'],
                'created_by_role' => $validatedData['created_by_role'],
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addMinutes($validatedData['time_limit']),
                'time_limit' => $validatedData['time_limit'],
                'is_negative_mark_applicable' => $request->input('is_negative_mark_applicable', false),
                'section_id' => $categories['section'],
                'exam_type_id' => $categories['exam_type'],
                'exam_sub_type_id' => $categories['exam_sub_type'],
                'group_id' => $categories['group'],
                'level_id' => $categories['level'],
                'lesson_id' => $categories['lesson'],
                'topic_id' => $categories['topic'],
                'sub_topic_id' => $categories['sub_topic'],
                'questions' => implode(',', $questions), // Store question IDs as a comma-separated string
            ]);

            // Create initial answer record with exam information and start time
            Answer::create([
                'examination_id' => $exam->id,
                'student_id' => $validatedData['student_id'],
                'type' => $validatedData['question_type'],
                'exam_start_time' => $exam->start_time,
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json(['exam' => $exam], 201);

        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while creating the exam.'], 500);
        }
    }


    private function filterQuestionsByCategories($query, $categories)
{
    $categoryMappings = [
        'section' => 'section_id',
        'exam_type' => 'exam_type_id',
        'exam_sub_type' => 'exam_sub_type_id',
        'group' => 'group_id',
        'level' => 'level_id',
        'lesson' => 'lesson_id',
        'topic' => 'topic_id',
        'sub_topic' => 'sub_topic_id',
    ];

    foreach ($categories as $key => $value) {
        if ($value) {
            $query->whereHas('questionable', function ($q) use ($key, $value, $categoryMappings) {
                $q->whereIn($categoryMappings[$key], explode(',', $value));
            });
        }
    }

    return $query;
}



    public function getExamQuestions($examId)
    {
        // Retrieve the exam record by ID
        $exam = Examination::findOrFail($examId);

        // Extract the question IDs from the `questions` field (comma-separated string)
        $questionIds = explode(',', $exam->questions);

        // Retrieve the questions based on the extracted IDs and load related MCQ and Creative options
        $questions = Question::whereIn('id', $questionIds)
            ->with(['mcqQuestions', 'creativeQuestions']) // Load relationships
            ->get();

        return response()->json([
            'exam' => $exam,
            'questions' => $questions
        ], 200);
    }




    public function finishExam(Request $request, $exam_id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'student_id' => 'required|integer',
        ]);

        $exam = Examination::findOrFail($exam_id);

        // Update the answers in the 'answers' table
        $answer = new Answer();
        $finishTime = now();
        $answer->update([
            'submission_time' => $finishTime,
        ]);
        // Update the exam to mark it as finished
        $exam->update([
            'student_ended_at' => $finishTime,
        ]);

        return response()->json(['message' => 'Exam finished and answers updated successfully', 'exam' => $exam], 200);
    }


    public function updateAnswer(Request $request)
{
    // Validate request data
    $validatedData = $request->validate([
        'student_id' => 'required|integer',
        'examination_id' => 'required|integer',
        'question_id' => 'required|integer',
        'option_id' => 'required', // Adjust validation based on type
    ]);

    $exam = Examination::find($validatedData['examination_id']);
    $type = $exam->type;
    if (!$exam) {
        return response()->json(['error' => 'Examination not found.'], 404);
    }

    $currentTime = Carbon::now();
    if ($currentTime->lt($exam->start_time) || $currentTime->gt($exam->end_time)) {
        return response()->json(['error' => 'Cannot update answer. Exam time has passed or has not started yet.'], 403);
    }

    // Fetch the answer record for the student and examination
    $answer = Answer::where('examination_id', $validatedData['examination_id'])
                    ->where('student_id', $validatedData['student_id'])
                    ->first();

    if (!$answer) {
        return response()->json(['error' => 'Answer record not found.'], 404);
    }

    DB::beginTransaction();

    try {
        // Update the JSON field based on answer type
        switch ($type) {
            case 'mcq':
                $answers = $answer->mcq_answers ?? [];
                $updated = false;
                foreach ($answers as &$ans) {
                    if ($ans['question_id'] == $validatedData['question_id']) {
                        $ans['submitted_mcq_option'] = $validatedData['option_id'];
                        $updated = true;
                        break;
                    }
                }
                if (!$updated) {
                    $answers[] = [
                        'question_id' => $validatedData['question_id'],
                        'mcq_question_id' => $validatedData['option_id'], // This assumes option_id is mcq_question_id
                        'submitted_mcq_option' => $validatedData['option_id'],
                    ];
                }
                $answer->mcq_answers = $answers;
                break;

            case 'creative':
                $answers = $answer->creative_answers ?? [];
                $updated = false;
                foreach ($answers as &$ans) {
                    if ($ans['question_id'] == $validatedData['question_id']) {
                        $ans['creative_question_option'] = $validatedData['option_id']; // Adjust as needed
                        $updated = true;
                        break;
                    }
                }
                if (!$updated) {
                    $answers[] = [
                        'question_id' => $validatedData['question_id'],
                        'creative_question_option' => $validatedData['option_id'], // Adjust as needed
                        'creative_question_answer' => null,
                        'creative_question_files' => [], // Adjust as needed
                    ];
                }
                $answer->creative_answers = $answers;
                break;

            case 'normal':
                $answers = $answer->normal_answers ?? [];
                $updated = false;
                foreach ($answers as &$ans) {
                    if ($ans['question_id'] == $validatedData['question_id']) {
                        $ans['normal_answer'] = $validatedData['option_id']; // Assuming option_id is the answer
                        $updated = true;
                        break;
                    }
                }
                if (!$updated) {
                    $answers[] = [
                        'question_id' => $validatedData['question_id'],
                        'normal_answer' => $validatedData['option_id'], // Assuming option_id is the answer
                    ];
                }
                $answer->normal_answers = $answers;
                break;

            default:
                DB::rollBack();
                return response()->json(['error' => 'Invalid answer type.'], 400);
        }

        $answer->is_answer_submitted = true; // Mark as answered if needed
        $answer->save();

        DB::commit();

        return response()->json(['message' => 'Answer updated successfully.'], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'An error occurred while updating the answer.'], 500);
    }
}



}
