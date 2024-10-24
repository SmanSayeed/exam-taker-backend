<?php

namespace App\Services\ExaminationService;

use App\Models\Answer;
use App\Models\Examination;
use App\Models\Question;
use App\Models\Questionable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ExaminationService
{

    public function startExam($validatedData, $request)
    {
        // Set default question limit
        $questionsLimit = $validatedData['questions_limit'] ?? 20;

        // Get categories from the request payload (categories are optional)
        $categories = $this->parseCategories($validatedData);

        // Fetch the question IDs using the updated filterQuestionsByCategories method
        $questionIds = $this->filterQuestionsByCategories($categories, $validatedData['type']);

        // If there are no matching questions, return an error
        if (empty($questionIds)) {
            return ['error' => 'No questions found for the given categories.', 'status' => 404];
        }

           // Get randomized and formatted questions
    $questionsList = $this->formatQuestionData($questionIds, $validatedData['type'])
    ->random($questionsLimit);
        // Get the result collection
        // Extract the question IDs from the list
        $questions = $questionsList->pluck('id')->toArray();

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create the exam in the examinations table
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
                'questions' => implode(',', $questions), // Storing question IDs as a comma-separated string
            ]);

            // Handle any failure during exam creation
            if (!$exam) {
                DB::rollBack();
                return ['error' => 'Failed to create exam.', 'status' => 500];
            }

            // Create an entry for the student's answer sheet
            Answer::create([
                'examination_id' => $exam->id,
                'student_id' => $validatedData['created_by'],
                'type' => $validatedData['type'],
                'exam_start_time' => $exam->start_time,
                'is_second_timer' => $request->is_second_timer ?? false,
            ]);

            // Commit the transaction
            DB::commit();

            // Return the created exam and the list of questions
            return ['exam' => $exam, 'questions_list' => $questionsList];
        } catch (\Exception $e) {
            // Handle any exception during the process
            \Log::error('Error creating exam: ' . $e);
            DB::rollBack();
            return ['error' => 'An error occurred while creating the exam.', 'status' => 500];
        }
    }



    protected function filterQuestionsByCategories($categories, $type)
    {
        // If no categories are provided, skip filtering by categories and fetch questions by type
        if ($categories === null) {
            return DB::table('questions')
                ->where('type', $type)
                ->pluck('id')
                ->toArray();
        }

        // Start building a query to get question IDs from the questionable table if categories exist
        $query = Questionable::query();

        // Apply filters based on the provided categories
        if (!empty($categories['section'])) {
            $query->whereIn('section_id', $categories['section']);
        }
        if (!empty($categories['exam_type'])) {
            $query->whereIn('exam_type_id', $categories['exam_type']);
        }
        if (!empty($categories['exam_sub_type'])) {
            $query->whereIn('exam_sub_type_id', $categories['exam_sub_type']);
        }
        if (!empty($categories['group'])) {
            $query->whereIn('group_id', $categories['group']);
        }
        if (!empty($categories['level'])) {
            $query->whereIn('level_id', $categories['level']);
        }
        if (!empty($categories['subject'])) {
            $query->whereIn('subject_id', $categories['subject']);
        }
        if (!empty($categories['lesson'])) {
            $query->whereIn('lesson_id', $categories['lesson']);
        }
        if (!empty($categories['topic'])) {
            $query->whereIn('topic_id', $categories['topic']);
        }
        if (!empty($categories['sub_topic'])) {
            $query->whereIn('sub_topic_id', $categories['sub_topic']);
        }

        // Get the question IDs from the questionable table
        $questionIds = $query->pluck('question_id')->toArray();

        // If no questions match the categories, return an empty array
        if (empty($questionIds)) {
            return [];
        }

        // Now filter questions by the required type from the questions table
        return DB::table('questions')
            ->whereIn('id', $questionIds)
            ->where('type', $type)
            ->pluck('id')
            ->toArray();
    }


    // Logic to start an exam
    // public function startExam($validatedData, $request)
    // {
    //     $questionsLimit = $validatedData['questions_limit'] ?? 20; // Default to 20 questions if not provided

    //     // Parse category-related IDs from request and store them as comma-separated strings
    //     $categories = $this->parseCategories($validatedData);
    //     // dd($categories);

    //     // Get the list of questions based on filtering
    //     $questions = Question::where('type', $validatedData['type'])->get()->pluck('id')->toArray();

    //     $query = new Questionable();

    //     $categoryQuestions = $this->filterQuestionsByCategories($query,$categories);


    //     // Randomly select the specified number of questions
    //     $questions = $query->inRandomOrder()->limit($questionsLimit)->pluck('question_id')->toArray();

    //     // Fetch the detailed question data
    //     $questionsList = $this->formatQuestionData($questions, $validatedData['type']); // Fetch question details

    //     DB::beginTransaction();

    //     try {
    //         // Create the exam
    //         $exam = Examination::create([
    //             'title' => $validatedData['title'],
    //             'description' => $request->input('description', null),
    //             'type' => $validatedData['type'],
    //             'is_paid' => $request->input('is_paid', false),
    //             'created_by' => $validatedData['created_by'],
    //             'created_by_role' => $validatedData['created_by_role'],
    //             'start_time' => Carbon::now(),
    //             'end_time' => Carbon::now()->addMinutes($validatedData['time_limit']),
    //             'time_limit' => $validatedData['time_limit'],
    //             'is_negative_mark_applicable' => $request->input('is_negative_mark_applicable', false),
    //             'section_id' => $categories['section'],
    //             'exam_type_id' => $categories['exam_type'],
    //             'exam_sub_type_id' => $categories['exam_sub_type'],
    //             'group_id' => $categories['group'],
    //             'subject_id' => $categories['subject'],
    //             'level_id' => $categories['level'],
    //             'lesson_id' => $categories['lesson'],
    //             'topic_id' => $categories['topic'],
    //             'sub_topic_id' => $categories['sub_topic'],
    //             'questions' => implode(',', $questions), // Store question IDs as a comma-separated string
    //         ]);

    //         if (!$exam) {
    //             DB::rollBack();
    //             return ['error' => 'Failed to create exam.', 'status' => 500];
    //         }

    //         // Create initial answer record
    //         Answer::create([
    //             'examination_id' => $exam->id,
    //             'student_id' => $validatedData['created_by'],
    //             'type' => $validatedData['type'],
    //             'exam_start_time' => $exam->start_time,
    //             'is_second_timer' => $request->is_second_timer ?? false,
    //         ]);

    //         DB::commit();

    //         return ['exam' => $exam, 'questions_list' => $questionsList]; // Return the exam and the detailed questions list
    //     } catch (\Exception $e) {
    //         \Log::error('Error creating exam: ' . $e);
    //         DB::rollBack();
    //         return ['error' => 'An error occurred while creating the exam.', 'status' => 500];
    //     }
    // }

    //  // Helper methods (e.g., filterQuestionsByCategories, formatQuestionData, parseCategories)
    //  private function filterQuestionsByCategories($query, $categories)
    //  {
    //      // Add filtering logic here based on the categories array
    //      if (!empty($categories['section'])) {
    //          $query->where('section_id', $categories['section']);
    //      }
    //      if (!empty($categories['exam_type'])) {
    //          $query->where('exam_type_id', $categories['exam_type']);
    //      }
    //      if (!empty($categories['group'])) {
    //          $query->where('group_id', $categories['group']);
    //      }
    //      if (!empty($categories['level'])) {
    //          $query->where('level_id', $categories['level']);
    //      }
    //      if (!empty($categories['group'])) {
    //          $query->where('group_id', $categories['group']);
    //      }
    //      if (!empty($categories['subject'])) {
    //          $query->where('subject_id', $categories['subject']);
    //      }
    //      if (!empty($categories['level'])) {
    //          $query->where('level_id', $categories['level']);
    //      }
    //      if (!empty($categories['lesson'])) {
    //          $query->where('lesson_id', $categories['lesson']);
    //      }
    //      if (!empty($categories['topic'])) {
    //          $query->where('topic_id', $categories['topic']);
    //      }
    //      if (!empty($categories['sub_topic'])) {
    //          $query->where('sub_topic_id', $categories['sub_topic']);
    //      }
    //      // Add other filters as needed

    //      return $query;
    //  }

    // Fetch exam details by ID
    public function getExamById($examId)
    {
        $exam = Examination::with('answers')->find($examId);

        if (!$exam) {
            return null;
        }

        $questions = explode(',', $exam->questions);
        $questionsList = $this->formatQuestionData($questions, $exam->type);

        return [
            'exam' => $exam,
            'questions_list' => $questionsList,
        ];
    }

    // Fetch exams by student ID
    public function getExamsByStudent($studentId, $withQuestionList = false)
    {
        $query = Examination::where('created_by', $studentId)
            ->where('created_by_role', 'student')
            ->orderBy('created_at', 'desc');

        $exams = $query->get();

        if ($withQuestionList) {
            foreach ($exams as $exam) {
                $questions = explode(',', $exam->questions);
                $exam->questions_list = $this->formatQuestionData($questions, $exam->type);
            }
        }

        return $exams;
    }

    // Fetch all exams with student info
    public function getAllExamsWithStudents($withQuestionList = false)
    {
        $query = Examination::with('answers', 'student')
            ->orderBy('created_at', 'desc');

        $exams = $query->get();

        if ($withQuestionList) {
            foreach ($exams as $exam) {
                $questions = explode(',', $exam->questions);
                $exam->questions_list = $this->formatQuestionData($questions, $exam->type);
            }
        }

        return $exams;
    }



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

    private function parseCategories($validatedData)
{
    // Parse and return the category-related IDs from the validated data
    $categories = [
        'section' => $validatedData['section_categories'] ?? null,
        'exam_type' => $validatedData['exam_type_categories'] ?? null,
        'exam_sub_type' => $validatedData['exam_sub_type_categories'] ?? null,
        'group' => $validatedData['group_categories'] ?? null,
        'subject' => $validatedData['subject_categories'] ?? null,
        'level' => $validatedData['level_categories'] ?? null,
        'lesson' => $validatedData['lesson_categories'] ?? null,
        'topic' => $validatedData['topic_categories'] ?? null,
        'sub_topic' => $validatedData['sub_topic_categories'] ?? null,
    ];

    // Check if all values are null
    if (count(array_filter($categories)) === 0) {
        return null; // If all are null, return null
    }

    return $categories;
}

    // Get the student's exam attempt
    public function getStudentExam($examinationId, $studentId)
    {
        return Answer::where('examination_id', $examinationId)
            ->where('student_id', $studentId)
            ->first();
    }

    // Retrieve formatted question data


    // Process MCQ answers
    public function processMcqAnswers($mcqAnswers, $totalMarks, $correctCount)
    {
        $processedMcqAnswers = [];

        foreach ($mcqAnswers as $ans) {
            try {
                $question = Question::find($ans['question_id']); // Get the first related MCQ question
                $correct_option_serial = null;
                $correct_option_id = null;
                // ---------------
                $allMcq = $question->mcqQuestions->all();
                foreach ($allMcq as $mcq) {
                    if ($mcq->is_correct == 1) {
                        $correct_option_serial = $mcq->mcq_option_serial;
                        $correct_option_id = $mcq->id;
                    }
                }

                $mcqAnswer = [
                    'question_id' => $question->id,
                    'mcq_question_id' => $ans['mcq_question_id'],
                    'submitted_mcq_option' => $ans['submitted_mcq_option'],
                    'is_submitted_correct' => false,
                    'correct_option_serial' => $correct_option_serial,
                    'correct_option_id' => $correct_option_id,
                    'description' => $question->description,
                    'mcq_question_text' => $mcqQuestion->mcq_question_text ?? null,
                    'mcq_option_serial' => $mcqQuestion->mcq_option_serial ?? null,
                    'mcq_images' => $mcqQuestion->mcq_images ?? null,
                ];

                if ($ans['mcq_question_id'] == $correct_option_id) {
                    $mcqAnswer['is_submitted_correct'] = true;
                    $correctCount++;
                    $totalMarks += $question->mark;
                }

                $processedMcqAnswers[] = $mcqAnswer;
            } catch (\Exception $e) {
                \Log::error('Error processing MCQ answer: ' . $e->getMessage());
                return []; // Return empty array on error
            }
        }
        return [$processedMcqAnswers, $totalMarks, $correctCount];
    }



    // Process Creative answers
    public function processCreativeAnswers($creativeAnswers)
    {
        $processedCreativeAnswers = [];

        foreach ($creativeAnswers as $ans) {
            $question = Question::find($ans['question_id']);
            try {
                $allCreative = $question->creativeQuestions->all();
                if (!$allCreative) {
                    throw new \Exception('Creative question not found for question ID: ' . $question->id);
                }

                $processedCreativeAnswer = [
                    'question_id' => $question->id,
                    'creative_question_id' => $creativeQuestion->id ?? null,
                    // 'creative_question_option' => $creativeAnswers->firstWhere('creative_question_id', $creativeQuestion->id)['creative_question_option'] ?? null,
                    // 'creative_question_text' => $creativeQuestion->creative_question_text ?? null,
                ];

                $processedCreativeAnswers[] = $processedCreativeAnswer;
            } catch (\Exception $e) {
                \Log::error('Error processing creative answer: ' . $e->getMessage());
                return []; // Return empty array on error
            }
        }

        return $processedCreativeAnswers;
    }


    // Process Normal answers
    public function processNormalAnswers($normalAnswers)
    {
        $processedNormalAnswers = [];

        foreach ($normalAnswers as $ans) {
            $question = Question::find($ans['question_id']);
            try {
                $processedNormalAnswers[] = [
                    'question_id' => $question->id,
                    'normal_answer_text' => $normalAnswers->firstWhere('question_id', $question->id)['normal_answer_text'] ?? null,
                ];
            } catch (\Exception $e) {
                \Log::error('Error processing normal answer: ' . $e->getMessage());
                return []; // Return empty array on error
            }
        }

        return $processedNormalAnswers;
    }

    // Update the answer record in the database
    public function updateAnswerRecord($answer, $mcqAnswers, $creativeAnswers, $normalAnswers, $totalMarks, $correctCount)
    {
        $answer->mcq_answers = $mcqAnswers;
        $answer->creative_answers = $creativeAnswers;
        $answer->normal_answers = $normalAnswers;
        $answer->submission_time = now();
        $answer->is_answer_submitted = true;
        $answer->total_marks = $totalMarks;
        $answer->correct_count = $correctCount;
        $answer->save();
    }

    // Prepare the response
    public function prepareResponse($examination, $mcqAnswers, $creativeAnswers, $normalAnswers,$totalMarks,$correctCount)
    {
        return [
            'examination' => $examination,
            'student' => $examination->student,
            'mcq_answers' => $mcqAnswers,
            'creative_answers' => $creativeAnswers,
            'normal_answers' => $normalAnswers,
            'total_marks' => $totalMarks,
            'correct_count' => $correctCount
        ];
    }
}
