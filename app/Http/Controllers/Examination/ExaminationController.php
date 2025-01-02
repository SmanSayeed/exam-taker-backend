<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Examination;
use App\Services\ExaminationService\ExaminationService;
use App\Http\Requests\StartExamRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExaminationController extends Controller
{
    protected $examinationService;

    // Inject the ExaminationService via the constructor
    public function __construct(ExaminationService $examinationService)
    {
        $this->examinationService = $examinationService;
    }

    // Start exam function
    public function startExam(StartExamRequest $request)
    {
        $validatedData = $request->validated();
        $maximum_free_exam = 2;

        if ($request->created_by_role != "student") {
            return response()->json(['error' => 'Only students can start an exam'], 400);
        }

        $student = Student::find($request->created_by);

        // Check if the student has exceeded the maximum free exam quota and paid quota.
        $isFreeQuotaExceeded = $student->exams_count >= $maximum_free_exam;
        $isPaidQuotaExceeded = $student->paid_exam_quota <= $student->exams_count;

        if ($isFreeQuotaExceeded && $isPaidQuotaExceeded) {
            return response()->json([
                'error' => 'You have reached the maximum number of exams. Please subscribe for more exams.',
                'quota_info' => [
                    'free_quota_exceeded' => $isFreeQuotaExceeded,
                    'paid_quota_exceeded' => $isPaidQuotaExceeded,
                ],
            ], 400);
        }

        // Delegate the exam creation logic to the service
        $result = $this->examinationService->startExam($validatedData, $request);

        // Check for errors in the result
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        // Only update the student's exam count if the exam is successfully created
        $student->update([
            'exams_count' => DB::raw('exams_count + 1'),
        ]);

        return response()->json([
            'exam' => $result['exam'],
            'questions_list' => $result['questions_list'],
            'quota_info' => [
                'free_quota_exceeded' => $isFreeQuotaExceeded,
                'paid_quota_exceeded' => $isPaidQuotaExceeded,
            ],
        ], 201);
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
}
