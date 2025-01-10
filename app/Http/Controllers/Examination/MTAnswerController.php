<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinishMTExamRequest;
use App\Models\Answer;
use App\Models\Examination;
use App\Models\ModelTest;
use App\Models\Student;
use App\Services\ExaminationService\MTExaminationService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;
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
        if (Carbon::now()->lt($examination->start_time)) {
            return ApiResponseHelper::error('The exam has not started yet.', 403);
        }

        // Check if exam time has ended
        if (Carbon::now()->gt($examination->end_time)) {
            return ApiResponseHelper::error('The exam has already ended.', 403);
        }

        if (!$examination->is_active) {
            return ApiResponseHelper::error('This examination is disabled.', 403);
        }

        // Process answers based on question type
        $totalMarks = 0;
        $correctCount = 0;

        $mcqAnswers = [];
        $creativeAnswers = $normalAnswers = null;
        $uploadedFilePath = null;

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
            // Handle file upload
            if ($request->hasFile('answer_file')) {
                $file = $request->file('answer_file');
                if ($file->isValid() && $file->extension() === 'pdf') {
                    $fileName = sprintf(
                        '%s_%s_%s_%s.pdf',
                        $examination->type,
                        $examination->name,
                        $request->student_id,
                        now()->format('Y_m_d_His')
                    );
                    $uploadedFilePath = $file->storeAs('answers', $fileName, 'public');
                } else {
                    return ApiResponseHelper::error('Invalid file uploaded. Only PDF files are allowed.', 400);
                }
            } else {
                return ApiResponseHelper::error('No answer file uploaded.', 400);
            }

            if ($examination->type == 'creative') {
                $creativeAnswers = ['file_path' => $uploadedFilePath];
            }

            if ($examination->type == 'normal') {
                $normalAnswers = ['file_path' => $uploadedFilePath];
            }
        }

        // Update the answer record
        $this->examService->updateAnswerRecord(
            $answer,
            $mcqAnswers,
            $creativeAnswers,
            $normalAnswers,
            $totalMarks,
            $correctCount,
            $uploadedFilePath
        );

        // Prepare and return the success response
        $response = $this->examService->prepareResponse(
            $examination,
            $mcqAnswers,
            $creativeAnswers,
            $normalAnswers,
            $totalMarks,
            $correctCount,
            $uploadedFilePath
        );

        return ApiResponseHelper::success($response, 'Exam finished successfully.');
    } catch (Exception $e) {
        // Log the error details
        Log::error('Error finishing exam', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        // Return a generic error response
        return ApiResponseHelper::error('An error occurred while processing the exam.', 500, [
            'details' => $e->getMessage()
        ]);
    }
}




    public function getStudentResult(Request $request, $modelTestId, $studentId)
    {
        try {
            // Fetch student and model test details
            $modelTest = ModelTest::findOrFail($modelTestId);
            $student = Student::findOrFail($studentId);

             // Check if the model test is finished based on the end_time (with 1 minute buffer)
        $currentTime = Carbon::now();
        $modelTestEndTimeWithBuffer = Carbon::parse($modelTest->end_time)->addMinute();

        // If the current time is before the adjusted end time, show an error
        if ($currentTime->lessThan($modelTestEndTimeWithBuffer)) {
            return ApiResponseHelper::error('Examination time is not yet over. Results cannot be viewed now.', 400);
        }


            // Fetch examinations related to the model test
            $examinations = Examination::where('model_test_id', $modelTestId)->get();

            $allExaminationDetails = [];
            $combinedResult = [
                'total_obtained_marks' => 0,
                'correct_answers' => 0,
                'wrong_answers' => 0,
                'skipped_questions' => 0,
                'total_questions' => 0,
            ];

            foreach ($examinations as $examination) {
                // Fetch answers for the student for this examination
                $answers = Answer::where('examination_id', $examination->id)
                    ->where('student_id', $studentId)
                    ->first();

                if ($answers) {
                    // Calculate metrics for this examination
                    $correctAnswers = $answers->correct_count ?? 0;
                    $totalQuestions = $answers->total_questions_count ?? 0;
                    $obtainedMarks = $answers->total_marks ?? 0;

                    $combinedResult['total_obtained_marks'] += $obtainedMarks;
                    $combinedResult['correct_answers'] += $correctAnswers;
                    $combinedResult['total_questions'] += $totalQuestions;
                    $combinedResult['wrong_answers'] += $totalQuestions - $correctAnswers - ($answers->is_answer_submitted ? 0 : 1);
                    $combinedResult['skipped_questions'] += $answers->is_answer_submitted ? 0 : 1;

                    $allExaminationDetails[] = [
                        'examination_id' => $examination->id,
                        'title' => $examination->title,
                        'type' => $examination->type,
                        'obtained_marks' => $obtainedMarks,
                        'correct_answers' => $correctAnswers,
                        'wrong_answers' => $totalQuestions - $correctAnswers,
                        'total_questions' => $totalQuestions,
                        'answers' => $answers,
                    ];
                }
            }

            // Fetch all students for merit list
            $studentsAnswers = Answer::whereIn('examination_id', $examinations->pluck('id'))
                ->groupBy('student_id')
                ->selectRaw('student_id, SUM(total_marks) as total_marks')
                ->orderBy('total_marks', 'desc')
                ->get();

            $meritList = $studentsAnswers->pluck('student_id')->toArray();
            $studentRank = array_search($studentId, $meritList) + 1;

            // Build response data
            $response = [
                'student_details' => $student,
                'model_test_details' => $modelTest,
                'all_examination_details' => $allExaminationDetails,
                'combined_result' => $combinedResult,
                'student_result' => [
                    'total_obtained_marks' => $combinedResult['total_obtained_marks'],
                    'merit_rank' => $studentRank,
                    'total_participants' => count($meritList),
                ],
            ];

            return ApiResponseHelper::success('Student result fetched successfully', $response);
        } catch (\Exception $e) {
            Log::error('Error fetching student result: ' . $e->getMessage());
            return ApiResponseHelper::error('Failed to fetch student result', 500);
        }
    }

public function getAllStudentsResult(Request $request, $modelTestId)
{
    try {
        // Fetch model test details
        $modelTest = ModelTest::findOrFail($modelTestId);

        // Fetch all examinations related to the model test
        $examinations = Examination::where('model_test_id', $modelTestId)->get();

        // Check if the model test is finished based on the end_time (with 1-minute buffer)
        $currentTime = Carbon::now();
        $modelTestEndTimeWithBuffer = Carbon::parse($modelTest->end_time)->addMinute();

        // If the current time is before the adjusted end time, show an error
        if ($currentTime->lessThan($modelTestEndTimeWithBuffer)) {
            return ApiResponseHelper::error('Examination time is not yet over. Results cannot be viewed now.', 400);
        }

        $combinedResults = [];
        $allExaminationDetails = [];
        $studentsResults = [];

        foreach ($examinations as $examination) {
            // Fetch all answers for the current examination
            $answers = Answer::where('examination_id', $examination->id)->get();

            foreach ($answers as $answer) {
                $student = Student::find($answer->student_id);
                if (!$student) continue;

                // Initialize student's result data
                if (!isset($studentsResults[$answer->student_id])) {
                    $studentsResults[$answer->student_id] = [
                        'student_details' => $student,
                        'total_obtained_marks' => 0,
                        'correct_answers' => 0,
                        'wrong_answers' => 0,
                        'skipped_questions' => 0,
                        'total_questions' => 0,
                        'examinations' => [],
                    ];
                }

                // Calculate metrics for this examination for the student
                $correctAnswers = $answer->correct_count ?? 0;
                $totalQuestions = $answer->total_questions_count ?? 0;
                $obtainedMarks = $answer->total_marks ?? 0;

                $studentsResults[$answer->student_id]['total_obtained_marks'] += $obtainedMarks;
                $studentsResults[$answer->student_id]['correct_answers'] += $correctAnswers;
                $studentsResults[$answer->student_id]['total_questions'] += $totalQuestions;
                $studentsResults[$answer->student_id]['wrong_answers'] += $totalQuestions - $correctAnswers - ($answer->is_answer_submitted ? 0 : 1);
                $studentsResults[$answer->student_id]['skipped_questions'] += $answer->is_answer_submitted ? 0 : 1;

                // Save the examination details for each student
                $studentsResults[$answer->student_id]['examinations'][] = [
                    'examination_id' => $examination->id,
                    'title' => $examination->title,
                    'type' => $examination->type,
                    'obtained_marks' => $obtainedMarks,
                    'correct_answers' => $correctAnswers,
                    'wrong_answers' => $totalQuestions - $correctAnswers,
                    'total_questions' => $totalQuestions,
                    'answers' => $answer,
                ];
            }
        }

        // Sort students by total marks in descending order
        $sortedStudents = collect($studentsResults)->sortByDesc(function ($studentResult) {
            return $studentResult['total_obtained_marks'];
        });

        // Create merit list with rank position
        $meritList = [];
        $rank = 1;
        foreach ($sortedStudents as $studentId => $studentResult) {
            $studentResult['merit_rank'] = $rank++;
            $studentsResults[$studentId] = $studentResult;
            $meritList[] = $studentResult;  // Add student with rank to the merit list
        }

        // Build the response data
        $response = [
            'model_test_details' => $modelTest,
            'all_examination_details' => $allExaminationDetails,
            'students_results' => $studentsResults,
            'merit_list' => $meritList,
            'total_participants' => count($studentsResults),
        ];

        return ApiResponseHelper::success('All students result fetched successfully', $response);
    } catch (\Exception $e) {
        Log::error('Error fetching all students results: ' . $e->getMessage());
        return ApiResponseHelper::error('Failed to fetch all students results', 500);
    }
}

}
