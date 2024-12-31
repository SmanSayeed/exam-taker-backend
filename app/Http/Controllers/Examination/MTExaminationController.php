<?php

namespace App\Http\Controllers\Examination;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MTStartExamRequest;
use App\Models\Answer;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Examination;
use App\Http\Requests\StartExamRequest;
use App\Services\ExaminationService\MTExaminationService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MTExaminationController extends Controller
{
    protected $examinationService;

    // Inject the ExaminationService via the constructor
    public function __construct(MTExaminationService $examinationService)
    {
        $this->examinationService = $examinationService;
    }

    // Start exam function
    public function createExam(MTStartExamRequest $request, $model_test_id)
    {
        $validatedData = $request->validated();

        if ($request->created_by_role != "admin") {
            return response()->json(['error' => 'Only admin can create this exam'], 400);
        }

        // Delegating the exam creation logic to the service
        $result = $this->examinationService->startExam($validatedData, $request);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json([
            'exam' => $result['exam'],
            'questions_list' => $result['questions_list'],
        ], 201);
    }

    public function getModelTestExams($model_test_id)
    {
        $exams = Examination::where('model_test_id', $model_test_id)->get();
        return ApiResponseHelper::success($exams, "Exams retrieved successfully");
    }

    public function studentStartExam(Request $request)
    {
        try {
            // Validation
            $validatedData = $request->validate([
                'is_second_timer' => 'boolean',
                'student_id' => 'required|exists:students,id',
                'exam_id' => 'required|exists:examinations,id',
            ]);

            $student_id = $validatedData['student_id'];
            $exam_id = $validatedData['exam_id'];
            $is_second_timer = $validatedData['is_second_timer'] ?? false;

            // Fetch exam and student
            $exam = Examination::find($exam_id);
            $student = Student::find($student_id);

            if (!$exam || !$student) {
                return ApiResponseHelper::error('Invalid exam or student ID', 404);
            }

            // Check if the exam has ended
            if (Carbon::now()->greaterThan(Carbon::parse($exam->end_time))) {
                return ApiResponseHelper::error('Exam time is already ended', 400);
            }

            // Check if the student has already started the exam
            $existingAnswer = Answer::where('student_id', $student_id)
                ->where('examination_id', $exam_id)
                ->first();


            if($existingAnswer->is_answer_submitted){
                return ApiResponseHelper::error('You have already submitted the answer', 400);
            }

            // Validate model test ID
            $model_test_id = $exam->model_test_id;
            if (!$model_test_id) {
                return ApiResponseHelper::error('Model test not found', 404);
            }

            // Check if the model test exists
            $model_test = DB::table('model_tests')->where('id', $model_test_id)->first();
            if (!$model_test) {
                return ApiResponseHelper::error('Model test not found', 404);
            }

            // Check if the package exists and is active
            $package = DB::table('packages')->where('id', $model_test->package_id)->first();
            if (!$package) {
                return ApiResponseHelper::error('Package not found', 404);
            }

            if (!$package->is_active) {
                return ApiResponseHelper::error('Package not active', 404);
            }

            // Verify subscription to the package
            if ($package->id) {
                $isSubscribed = $student->subscriptions()->where('package_id', $package->id)->exists();
                if (!$isSubscribed) {
                    return ApiResponseHelper::error('You are not subscribed to this package', 403);
                }
            }

            // Call the service to start the exam
            $result = $this->examinationService->studentStartExam($student_id, $exam_id);

            // Handle success response
            return ApiResponseHelper::success($result, "Exam started successfully");
        } catch (\Throwable $e) {
            // Log the error details
            Log::error('Error starting exam', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return a generic error response
            return ApiResponseHelper::error('Internal Server Error', 500, ['details' => $e->getMessage()]);
        }
    }

    // Get exam by ID function
    public function getExamById($examId)
    {
        $result = $this->examinationService->getExamById($examId);

        if (!$result) {
            return response()->json(['error' => 'Exam not found'], 404);
        }

        return response()->json([
            'exam' => $result['exam'],
            'questions_list' => $result['questions_list'],
        ]);
    }

    // Get exams by student ID function
    public function getExamsByStudent($studentId, $withQuestionList = false)
    {
        $exams = $this->examinationService->getExamsByStudent($studentId, $withQuestionList);
        return response()->json(['exams' => $exams]);
    }

    // Get all exams with student information function
    public function getAllExamsWithStudents($withQuestionList = 0)
    {
        $exams = $this->examinationService->getAllExamsWithStudents($withQuestionList);
        return response()->json(['exams' => $exams]);
    }

    public function getMTResult($student_id, $model_test_id)
    {
        try {
            // Fetch the examinations related to the model test
            $examinations = DB::table('examinations')->where('id', $model_test_id)->get();


            if ($examinations->isEmpty()) {
                return ApiResponseHelper::error("No examinations found for the provided model test ID.", 404);
            }

            $results = [];

            // Iterate over the examinations and fetch the answers
            foreach ($examinations as $exam) {
                $answer = Answer::where('examination_id', $exam->id)->first();
                if ($answer) {
                    $results[] = $answer; // Add to results if an answer exists
                }
            }

            if (empty($results)) {
                return ApiResponseHelper::error("No result found for the given examinations.", 404);
            }

            return ApiResponseHelper::success($results);

        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            \Log::error('Error fetching model test results: ' . $e->getMessage(), [
                'student_id' => $student_id,
                'model_test_id' => $model_test_id
            ]);

            // Return a generic error response
            return ApiResponseHelper::error("An error occurred while fetching the results. Please try again later.", 500);
        }
    }

}
