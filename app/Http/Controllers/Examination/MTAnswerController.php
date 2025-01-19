<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinishMTExamRequest;
use App\Models\Answer;
use App\Models\Examination;
use App\Models\ModelTest;
use App\Models\Question;
use App\Models\Student;
use App\Services\ExaminationService\MTExaminationService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class MTAnswerController extends Controller
{
    protected $examService;

    public function __construct(MTExaminationService $examService)
    {
        $this->examService = $examService;
    }

    public function finishExam(FinishMTExamRequest $request)
{
    try {
        // Retrieve student exam and examination details
        $answer = $this->examService->getStudentExam($request->examination_id, $request->student_id);
        if (!$answer) {
            return ApiResponseHelper::error('No active exam found for this student.', 404);
        }

        $examination = Examination::find($request->examination_id);
        if (!$examination) {
            return ApiResponseHelper::error('Examination not found.', 404);
        }

        // Check if exam has started
        // if (Carbon::now()->lt($examination->start_time)) {
        //     return ApiResponseHelper::error('The exam has not started yet.', 403);
        // }

        // // Check if exam time has ended
        // if (Carbon::now()->gt($examination->end_time)) {
        //     return ApiResponseHelper::error('The exam has already ended.', 403);
        // }

        if (!$examination->is_active) {
            return ApiResponseHelper::error('This examination is disabled.', 403);
        }

        // Process answers based on question type
        $totalMarks = 0;
        $correctCount = 0;

        $mcqAnswers = [];
        $creativeAnswers = $normalAnswers = null;

        if ($examination->type == 'mcq') {
            [$mcqAnswers, $totalMarks, $correctCount] = $this->examService->processMcqAnswers(
                $request->mcq_answers,
                $totalMarks,
                $correctCount,
                $examination
            );
            if (empty($mcqAnswers)) {
                return ApiResponseHelper::error('Error processing MCQ answers.', 400);
            }
        }

        if (in_array($examination->type, ['creative', 'normal'])) {
            $fileUrl = $request->input('file_url');

            if (!$fileUrl) {
                return ApiResponseHelper::error('File URL is required for creative/normal exams.', 400);
            }

            if ($examination->type == 'creative') {
                $creativeAnswers = ['file_url' => $fileUrl];
            }

            if ($examination->type == 'normal') {
                $normalAnswers = ['file_url' => $fileUrl];
            }
        }

        // Update the answer record
        $this->examService->updateAnswerRecord(
            $answer,
            $mcqAnswers,
            $creativeAnswers,
            $normalAnswers,
            $totalMarks,
            $correctCount
        );

        // Prepare and return the success response
        $response = $this->examService->prepareResponse(
            $examination,
            $mcqAnswers,
            $creativeAnswers,
            $normalAnswers,
            $totalMarks,
            $correctCount
        );

        return ApiResponseHelper::success($response, 'Exam finished successfully.');

    } catch (Exception $e) {
        Log::error('Error finishing exam', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return ApiResponseHelper::error('An error occurred while processing the exam.', 500, [
            'details' => $e->getMessage()
        ]);
    }
}


public function getStudentResult(Request $request, $studentId, $modelTestId)
{
    try {
        // First check if the student exists
        $student = Student::find($studentId);
        if (!$student) {
            return ApiResponseHelper::error("Student not found", 404);
        }

        // Then check if the model test exists
        $modelTest = ModelTest::find($modelTestId);
        if (!$modelTest) {
            return ApiResponseHelper::error("Model test not found", 404);
        }

        // Fetch examinations related to the model test
        $examinations = Examination::where('model_test_id', $modelTestId)->get();

        if ($examinations->isEmpty()) {
            return ApiResponseHelper::error('No examinations found for this model test.', 404);
        }

        // Get all answers for this student
        $answers = Answer::whereIn('examination_id', $examinations->pluck('id'))
            ->where('student_id', $studentId)
            ->get()
            ->keyBy('examination_id');

        if ($answers->isEmpty()) {
            return ApiResponseHelper::error('No answers found for this student in this model test.', 404);
        }

        $examinationDetails = [];
        $totalMarks = 0;

        foreach ($examinations as $examination) {
            $answer = $answers->get($examination->id);
            if (!$answer) {
                continue;
            }

            // Get questions details
            $questionIds = explode(',', $examination->questions);
            $questions = Question::whereIn('id', $questionIds)
                ->with(['mcqQuestions', 'creativeQuestions'])
                ->get()
                ->map(function ($question) use ($answer) {
                    $questionData = [
                        'id' => $question->id,
                        'title' => $question->title,
                        'description' => $question->description,
                        'type' => $question->type,
                        'mark' => $question->mark,
                        'images' => $question->images,
                        'tags' => $question->tags
                    ];

                    // Add type-specific question details and student answers
                    if ($question->type === 'mcq') {
                        $questionData['mcq_options'] = $question->mcqQuestions;
                        if (!empty($answer->mcq_answers)) {
                            $studentAnswer = collect($answer->mcq_answers)
                                ->firstWhere('question_id', $question->id);
                            if ($studentAnswer) {
                                $questionData['student_answer'] = $studentAnswer;
                            }
                        }
                    } elseif ($question->type === 'creative') {
                        $questionData['creative_parts'] = $question->creativeQuestions;
                        if (!empty($answer->creative_answers)) {
                            $questionData['student_answer'] = $answer->creative_answers;
                        }
                    } elseif ($question->type === 'normal') {
                        if (!empty($answer->normal_answers)) {
                            $questionData['student_answer'] = $answer->normal_answers;
                        }
                    }

                    return $questionData;
                });

            $examDetail = [
                'examination_id' => $examination->id,
                'title' => $examination->title,
                'description' => $examination->description,
                'type' => $examination->type,
                'start_time' => $examination->start_time,
                'end_time' => $examination->end_time,
                'time_limit' => $examination->time_limit,
                'is_negative_mark_applicable' => $examination->is_negative_mark_applicable,
                'is_optional' => $examination->is_optional,
                'is_active' => $examination->is_active,
                'obtained_marks' => $answer->total_marks ?? 0,
                'correct_answers' => $answer->correct_count ?? 0,
                'total_questions' => count($questionIds),
                'submission_time' => $answer->submission_time,
                'is_answer_submitted' => $answer->is_answer_submitted,
                'is_exam_time_out' => $answer->is_exam_time_out,
                'exam_start_time' => $answer->exam_start_time,
                'is_reviewed' => $examination->is_reviewed,
                'questions' => $questions
            ];

            $examinationDetails[] = $examDetail;
            $totalMarks += $answer->total_marks ?? 0;
        }

        // Calculate merit position using DB transaction for consistency
        $meritPosition = DB::transaction(function () use ($examinations, $studentId) {
            return Answer::whereIn('examination_id', $examinations->pluck('id'))
                ->groupBy('student_id')
                ->select('student_id', DB::raw('SUM(total_marks) as total_marks'))
                ->orderByDesc('total_marks')
                ->get()
                ->search(function($item) use ($studentId) {
                    return $item->student_id == $studentId;
                }) + 1;
        });

        $totalParticipants = Answer::whereIn('examination_id', $examinations->pluck('id'))
            ->distinct('student_id')
            ->count('student_id');

        $response = [
            'student_details' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
                'profile_image' => $student->profile_image,
                'section_id' => $student->section_id,
                'active_status' => $student->active_status,
                'exams_count' => $student->exams_count
            ],
            'model_test_details' => [
                'id' => $modelTest->id,
                'title' => $modelTest->title,
                'description' => $modelTest->description,
                'start_time' => $modelTest->start_time,
                'end_time' => $modelTest->end_time,
                'status' => $modelTest->status
            ],
            'examination_details' => $examinationDetails,
            'result_summary' => [
                'total_marks' => $totalMarks,
                'merit_position' => $meritPosition,
                'total_participants' => $totalParticipants
            ]
        ];

        return ApiResponseHelper::success('Student result fetched successfully', $response);

    } catch (\Exception $e) {
        Log::error('Error fetching student result: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'model_test_id' => $modelTestId,
            'student_id' => $studentId
        ]);

        // Return a more specific error message based on the exception
        $errorMessage = 'Failed to fetch student result';
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $errorMessage = 'Student or model test not found';
        }

        return ApiResponseHelper::error($errorMessage, 500);
    }
}
public function getAllStudentsResult(Request $request, $modelTestId)
{
    try {
        // Fetch model test details
        $modelTest = ModelTest::findOrFail($modelTestId);

        // Fetch all examinations related to the model test
        $examinations = Examination::where('model_test_id', $modelTestId)->get();

        // Get all valid student IDs who have answers for this model test's examinations
        $studentIds = Answer::whereIn('examination_id', $examinations->pluck('id'))
            ->distinct('student_id')
            ->pluck('student_id');

        // Fetch all relevant students in one query
        $students = Student::whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        $studentsResults = [];

        foreach ($examinations as $examination) {
            // Get questions details once for the examination
            $questionIds = explode(',', $examination->questions);
            $questions = Question::whereIn('id', $questionIds)
                ->with(['mcqQuestions', 'creativeQuestions'])
                ->get();

            // Fetch all answers for this examination
            $answers = Answer::where('examination_id', $examination->id)
                ->whereIn('student_id', $studentIds)
                ->get();

            foreach ($answers as $answer) {
                // Skip if student doesn't exist
                if (!isset($students[$answer->student_id])) {
                    continue;
                }

                $student = $students[$answer->student_id];

                if (!isset($studentsResults[$answer->student_id])) {
                    $studentsResults[$answer->student_id] = [
                        'student_details' => [
                            'id' => $student->id,
                            'name' => $student->name,
                            'email' => $student->email,
                            'phone' => $student->phone,
                            'profile_image' => $student->profile_image,
                            'section_id' => $student->section_id,
                            'active_status' => $student->active_status
                        ],
                        'total_marks' => 0,
                        'examinations' => []
                    ];
                }

                // Map questions with student answers
                $questionDetails = $questions->map(function ($question) use ($answer) {
                    $questionData = [
                        'id' => $question->id,
                        'title' => $question->title,
                        'description' => $question->description,
                        'type' => $question->type,
                        'mark' => $question->mark
                    ];

                    if ($question->type === 'mcq') {
                        $questionData['mcq_options'] = $question->mcqQuestions;
                        if (!empty($answer->mcq_answers)) {
                            $studentAnswer = collect($answer->mcq_answers)
                                ->firstWhere('question_id', $question->id);
                            if ($studentAnswer) {
                                $questionData['student_answer'] = $studentAnswer;
                            }
                        }
                    } elseif ($question->type === 'creative') {
                        $questionData['creative_parts'] = $question->creativeQuestions;
                        if (!empty($answer->creative_answers)) {
                            $questionData['student_answer'] = $answer->creative_answers;
                        }
                    } elseif ($question->type === 'normal') {
                        if (!empty($answer->normal_answers)) {
                            $questionData['student_answer'] = $answer->normal_answers;
                        }
                    }

                    return $questionData;
                });

                // Add examination details to student's result
                $studentsResults[$answer->student_id]['examinations'][] = [
                    'examination_id' => $examination->id,
                    'title' => $examination->title,
                    'type' => $examination->type,
                    'obtained_marks' => $answer->total_marks ?? 0,
                    'correct_answers' => $answer->correct_count ?? 0,
                    'total_questions' => count($questionIds),
                    'submission_time' => $answer->submission_time,
                    'is_answer_submitted' => $answer->is_answer_submitted,
                    'is_exam_time_out' => $answer->is_exam_time_out,
                    'exam_start_time' => $answer->exam_start_time,
                    'is_reviewed' => $examination->is_reviewed,
                    'questions' => $questionDetails
                ];

                $studentsResults[$answer->student_id]['total_marks'] += $answer->total_marks ?? 0;
            }
        }

        // Sort students by total marks and assign ranks
        $sortedResults = collect($studentsResults)
            ->sortByDesc('total_marks')
            ->values();

        // Add rank to each student
        $rankedResults = $sortedResults->map(function ($result, $index) {
            $result['merit_rank'] = $index + 1;
            return $result;
        });

        $response = [
            'model_test_details' => [
                'id' => $modelTest->id,
                'title' => $modelTest->title,
                'start_time' => $modelTest->start_time,
                'end_time' => $modelTest->end_time,
                'description' => $modelTest->description,
                'status' => $modelTest->status
            ],
            'students_results' => $rankedResults,
            'total_participants' => count($studentsResults)
        ];

        return ApiResponseHelper::success('All students results fetched successfully', $response);

    } catch (\Exception $e) {
        Log::error('Error fetching all students results: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'model_test_id' => $modelTestId
        ]);
        return ApiResponseHelper::error('Failed to fetch all students results', 500);
    }
}

}
