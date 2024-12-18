<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinishExamRequest;
use App\Models\Examination;
use App\Services\ExaminationService\ExaminationService;
use App\Services\ExaminationService\MTExaminationService;
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



    public function finishExam(FinishExamRequest $request)
    {
        try {
            // Retrieve student exam and examination details
            $answer = $this->examService->getStudentExam($request->examination_id, $request->student_id);
            if (!$answer) {
                return response()->json(['error' => 'No active exam found for this student.'], 404);
            }

            $examination = Examination::find($request->examination_id);
            if (!$examination) {
                return response()->json(['error' => 'Examination not found.'], 404);
            }



            // Check if exam has started
            if (Carbon::now()->lt($examination->start_time)) {
                return response()->json(['error' => 'The exam has not started yet.'], 403);
            }

            // Check if exam time has ended
            if (Carbon::now()->gt($examination->end_time)) {
                return response()->json(['error' => 'The exam has already ended.'], 403);
            }

            if(!$examination->is_active){
                return response()->json(['error' => 'This examination is disabled'], 403);
            }

            // Process answers based on question type
            $totalMarks = 0;
            $correctCount = 0;

            // Get questions with comma separated id if needed
            // $formattedQuestions = $this->examService->formatQuestionData(explode(',', $examination->questions), $examination->type);
            // $totalQuestionsCount = count($formattedQuestions);

            // Handle different types of answers
            $mcqAnswers = $creativeAnswers = $normalAnswers = [];

            if ($examination->type == 'mcq') {
                [$mcqAnswers, $totalMarks, $correctCount] = $this->examService->processMcqAnswers($request->mcq_answers, $totalMarks, $correctCount,$examination);
                if (empty($mcqAnswers)) {
                    return response()->json(['error' => 'Error processing MCQ answers.'], 400);
                }
            }

            if ($examination->type == 'creative') {
                $creativeAnswers = $this->examService->processCreativeAnswers($request->creative_answers);
                if (empty($creativeAnswers)) {
                    return response()->json(['error' => 'Error processing creative answers.'], 400);
                }
            }
            if ($examination->type == 'normal') {
                $normalAnswers = $this->examService->processNormalAnswers($request->normal_answers);
                if (empty($normalAnswers)) {
                    return response()->json(['error' => 'Error processing normal answers.'], 400);
                }
            }
            // Update the answer record
            $this->examService->updateAnswerRecord($answer, $mcqAnswers, $creativeAnswers, $normalAnswers, $totalMarks, $correctCount);

            // Return response
            return response()->json($this->examService->prepareResponse($examination, $mcqAnswers, $creativeAnswers, $normalAnswers,$totalMarks, $correctCount));

        } catch (Exception $e) {
            Log::error('Error finishing exam: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the exam.'.$e->getMessage()], 500);
        }
    }

}
