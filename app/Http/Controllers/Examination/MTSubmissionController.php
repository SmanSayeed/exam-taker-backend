<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Examination;
use App\Models\MTAnswerFile;
use App\Helpers\ApiResponseHelper;
use App\Services\ExaminationService\MTExaminationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MTSubmissionController extends Controller
{
    protected $examinationService;

    public function __construct(MTExaminationService $examinationService)
    {
        $this->examinationService = $examinationService;
    }

    public function getSubmissionsByExam($mtId, $examId)
    {
        try {
            // Get examination with questions
            $examData = $this->examinationService->getExamById($examId);
            if (!$examData || $examData['exam']->model_test_id != $mtId) {
                return ApiResponseHelper::error('Examination not found.', 404);
            }

            // Get all submitted answers
            $answers = Answer::where('examination_id', $examId)
                ->where('is_answer_submitted', true)
                ->with(['student:id,name,email,phone,profile_image,section_id'])
                ->get();

            if ($answers->isEmpty()) {
                return ApiResponseHelper::error('No submissions found for this exam.', 404);
            }

            // Prepare examination data
            $exam = $examData['exam'];
            $questions = $examData['questions_list'];

            // Format response data
            $responseData = [
                'examination' => [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'description' => $exam->description,
                    'type' => $exam->type,
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                    'time_limit' => $exam->time_limit,
                    'is_negative_mark_applicable' => $exam->is_negative_mark_applicable,
                    'total_questions' => $questions->count(),
                    'total_submissions' => $answers->count()
                ],
                'answers' => []
            ];

            // Process each answer
            foreach ($answers as $answer) {
                $answerData = [
                    'answer_id' => $answer->id,
                    'student' => [
                        'id' => $answer->student->id,
                        'name' => $answer->student->name,
                        'email' => $answer->student->email,
                        'phone' => $answer->student->phone,
                        'profile_image' => $answer->student->profile_image,
                        'section_id' => $answer->student->section_id
                    ],
                    'submission_details' => [
                        'exam_start_time' => $answer->exam_start_time,
                        'submission_time' => $answer->submission_time,
                        'is_exam_time_out' => $answer->is_exam_time_out,
                        'total_marks' => $answer->total_marks,
                        'correct_count' => $answer->correct_count,
                        'total_questions_count' => $answer->total_questions_count,
                        'status' => $answer->status,
                        'comments' => $answer->comments
                    ]
                ];

                // Add type-specific data
                if ($exam->type === 'mcq') {
                    $answerData['questions'] = $questions->map(function ($question) use ($answer) {
                        $submittedAnswer = collect($answer->mcq_answers)->firstWhere('question_id', $question->id);

                        $questionData = [
                            'id' => $question->id,
                            'description' => $question->description,
                            'mark' => $question->mark,
                            'options' => $question->mcqQuestions->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'mcq_option_serial' => $option->mcq_option_serial,
                                    'mcq_option_text' => $option->mcq_option_text,
                                    'is_correct' => $option->is_correct
                                ];
                            })
                        ];

                        if ($submittedAnswer) {
                            $questionData['submitted_answer'] = [
                                'submitted_option' => $submittedAnswer['submitted_mcq_option'],
                                'is_correct' => $submittedAnswer['is_submitted_correct'],
                                'correct_option_serial' => $submittedAnswer['correct_option_serial']
                            ];
                        }

                        return $questionData;
                    });
                } else {
                    // For creative/normal type
                    $answerData['questions'] = $questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'description' => $question->description,
                            'mark' => $question->mark
                        ];
                    });

                    // Add file information
                    $file = MTAnswerFile::where('student_id', $answer->student_id)
                        ->where('exam_id', $examId)
                        ->first();

                    if ($file) {
                        $answerData['submitted_file'] = [
                            'file_url' => $file->file_url,
                            'cdn_url' => config('filesystems.disks.public.cdn_url', config('app.url').'/storage') . '/' .
                                ltrim(str_replace('/storage/', '', $file->file_url), '/'),
                            'original_filename' => $file->original_filename,
                            'uploaded_at' => $file->created_at
                        ];
                    }

                    // Add type-specific answers
                    if ($exam->type === 'creative') {
                        $answerData['creative_answers'] = $answer->creative_answers;
                    } else {
                        $answerData['normal_answers'] = $answer->normal_answers;
                    }
                }

                $responseData['answers'][] = $answerData;
            }

            return ApiResponseHelper::success($responseData, 'Submissions retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving submissions', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mt_id' => $mtId,
                'exam_id' => $examId
            ]);

            return ApiResponseHelper::error('An error occurred while retrieving submissions.', 500);
        }
    }

    public function getStudentSubmission($mtId, $examId, $studentId)
    {
        try {
            // Get examination details with questions
            $examData = $this->examinationService->getExamById($examId);

            if (!$examData || $examData['exam']->model_test_id != $mtId) {
                return ApiResponseHelper::error('Examination not found.', 404);
            }

            // Get student's answer
            $answer = $this->examinationService->getStudentExam($examId, $studentId);

            if (!$answer || !$answer->is_answer_submitted) {
                return ApiResponseHelper::error('No submitted answer found for this student.', 404);
            }

            $responseData = [
                'examination' => $examData['exam'],
                'type' => $answer->type,
                'submission_details' => [
                    'start_time' => $answer->exam_start_time,
                    'submission_time' => $answer->submission_time,
                    'is_exam_time_out' => $answer->is_exam_time_out,
                    'total_marks' => $answer->total_marks,
                    'correct_count' => $answer->correct_count
                ]
            ];

            // Add type-specific data
            if ($answer->type === 'mcq') {
                $questions = $examData['questions_list'];
                $submittedAnswers = $answer->mcq_answers;

                $responseData['questions'] = $questions->map(function ($question) use ($submittedAnswers) {
                    $submittedAnswer = collect($submittedAnswers)->firstWhere('question_id', $question->id);

                    return [
                        'id' => $question->id,
                        'description' => $question->description,
                        'mark' => $question->mark,
                        'mcq_options' => $question->mcqQuestions,
                        'submitted_answer' => $submittedAnswer ? [
                            'submitted_option' => $submittedAnswer['submitted_mcq_option'],
                            'is_correct' => $submittedAnswer['is_submitted_correct'],
                            'correct_option' => $submittedAnswer['correct_option_serial']
                        ] : null
                    ];
                });
            } else {
                // For creative/normal type, get the file information
                $file = MTAnswerFile::where('student_id', $studentId)
                    ->where('exam_id', $examId)
                    ->first();

                if ($file) {
                    $responseData['submitted_file'] = [
                        'file_url' => $file->file_url,
                        'cdn_url' => config('filesystems.disks.public.cdn_url', config('app.url').'/storage') . '/' .
                            ltrim(str_replace('/storage/', '', $file->file_url), '/'),
                        'original_filename' => $file->original_filename,
                        'uploaded_at' => $file->created_at
                    ];
                }

                // Add questions for reference
                $responseData['questions'] = $examData['questions_list'];
            }

            return ApiResponseHelper::success($responseData, 'Student submission retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving student submission', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mt_id' => $mtId,
                'exam_id' => $examId,
                'student_id' => $studentId
            ]);

            return ApiResponseHelper::error('An error occurred while retrieving the submission.', 500);
        }
    }
}
