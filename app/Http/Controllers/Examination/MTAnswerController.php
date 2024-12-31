<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinishMTExamRequest;
use App\Models\Examination;
use App\Services\ExaminationService\MTExaminationService;
use App\Helpers\ApiResponseHelper;
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

            $mcqAnswers = $creativeAnswers = $normalAnswers = [];

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

            if ($examination->type == 'creative') {
                $creativeAnswers = $this->examService->processCreativeAnswers($request->creative_answers);
                if (empty($creativeAnswers)) {
                    return ApiResponseHelper::error('Error processing creative answers.', 400);
                }
            }

            if ($examination->type == 'normal') {
                $normalAnswers = $this->examService->processNormalAnswers($request->normal_answers);
                if (empty($normalAnswers)) {
                    return ApiResponseHelper::error('Error processing normal answers.', 400);
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
}
