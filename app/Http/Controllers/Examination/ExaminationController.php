<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Models\Examination;
use App\Services\ExaminationService\ExaminationService;
use App\Http\Requests\StartExamRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        if ($request->created_by_role != "student") {
            return response()->json(['error' => 'Only students can start an exam'], 400);
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
