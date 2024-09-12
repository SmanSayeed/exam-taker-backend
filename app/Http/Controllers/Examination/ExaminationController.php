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

    public function formatQuestionData($questions, $type = null)
{
    $query = Question::whereIn('id', $questions);

    // Filter by type, if provided
    if ($type) {
        $query->where('type', $type);
    }

    // Include related data based on the question type
    switch ($type) {
        case 'mcq':
            $query->with('mcqQuestions');
            break;
        case 'creative':
            $query->with('creativeQuestions');
            break;
        case 'normal':
            // No additional relations for normal questions
            break;
        default:
            // Include both mcq and creative options
            $query->with(['creativeQuestions', 'mcqQuestions']);
            break;
    }

    // Always include the attachable relation
    return $query->with('attachable')->get();
}


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
        $query = Question::where('type', $validatedData['type']);
        $query = $this->filterQuestionsByCategories($query, $categories);

        // Randomly select the specified number of questions
        $questions = $query->inRandomOrder()->limit($questionsLimit)->pluck('id')->toArray();

        // Fetch the detailed question data
        $questionsList = Question::whereIn('id', $questions)->with('attachable')->get();

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

            if (!$exam) {
                // Rollback and return error if exam creation fails
                DB::rollBack();
                return response()->json(['error' => 'Failed to create exam.'], 500);
            }

            // Create initial answer record with exam information and start time
            Answer::create([
                'examination_id' => $exam->id,
                'student_id' => $validatedData['student_id'],
                'type' => $validatedData['type'],
                'exam_start_time' => $exam->start_time,
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'exam' => $exam,
                'questions_list' => $questionsList // Add the detailed questions list to the response
            ], 201);

        } catch (\Exception $e) {
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



   // 1. Get exam by exam ID with questions_list
   public function getExamById($examId)
   {
       $exam = Examination::with('answers')->find($examId);

       if (!$exam) {
           return response()->json(['error' => 'Exam not found'], 404);
       }

       $questions = explode(',', $exam->questions);

       // Use the helper function to format the question data
       $questionsList = $this->formatQuestionData($questions, $exam->type);

       return response()->json([
           'exam' => $exam,
           'questions_list' => $questionsList
       ]);
   }


   // 2. Get exams by student ID with optional questions_list
   public function getExamsByStudent($studentId, $withQuestionList = false)
   {
       $role = 'student';
       // Fetch exams created by the specific student, ordered by created_at in descending order
       $query = Examination::where('created_by', $studentId)
                           ->where('created_by_role', $role)
                           ->orderBy('created_at', 'desc');

       // If the user requested questions_list, include it
       if ($withQuestionList) {
           $exams = $query->get(); // Get exams first
           foreach ($exams as $exam) {
               $questions = explode(',', $exam->questions); // Assumes questions are stored as a comma-separated string
               $exam->questions_list = $this->formatQuestionData($questions, $exam->type); // Format questions list based on type
           }
       } else {
           // Include answers relationship
           $exams = $query->with('answers')->get();
       }

       return response()->json(['exams' => $exams]);
   }


   // 3. Get all exams with student information
   public function getAllExamsWithStudents($withQuestionList = 0)
   {
       // Fetch all exams ordered by created_at in descending order
       $query = Examination::with('answers', 'student') // Use the 'student' relationship
                           ->orderBy('created_at', 'desc');

       // If the user requested questions_list, include it
       if ($withQuestionList) {
           $exams = $query->get(); // Get exams first
           foreach ($exams as $exam) {
               $questions = explode(',', $exam->questions); // Assumes questions are stored as a comma-separated string
               $exam->questions_list = $this->formatQuestionData($questions, $exam->type); // Format questions list based on type
           }
       } else {
           $exams = $query->get();
       }

       return response()->json(['exams' => $exams]);
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
