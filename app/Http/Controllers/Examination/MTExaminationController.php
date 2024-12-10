<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\MTStartExamRequest;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Models\Examination;
use App\Http\Requests\StartExamRequest;
use App\Services\ExaminationService\MTExaminationService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MTExaminationController extends Controller
{
    protected $examinationService;

    // Inject the ExaminationService via the constructor
    public function __construct(MTExaminationService $examinationService)
    {
        $this->examinationService = $examinationService;
    }

    // Start exam function
    public function createExam(MTStartExamRequest $request,$model_test_id)
    {
        $validatedData = $request->validated();

        if ($request->created_by_role != "admin") {
            return response()->json(['error' => 'Only admin can start this exam'], 400);
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
