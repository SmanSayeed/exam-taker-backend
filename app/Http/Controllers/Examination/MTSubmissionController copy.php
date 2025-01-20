<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Examination;
use App\Models\ModelTest;
use App\Models\MTAnswerFile;
use App\Helpers\ApiResponseHelper;
use App\Models\Question;
use App\Services\ExaminationService\MTExaminationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
            // Get model test details first
            $modelTest = ModelTest::findOrFail($mtId);

            // Get examination
            $examination = Examination::where('id', $examId)
                ->where('model_test_id', $mtId)
                ->firstOrFail();

            // Get questions with their options
            $questionIds = is_string($examination->questions) ?
                explode(',', $examination->questions) :
                $examination->questions;

            $questions = Question::whereIn('id', $questionIds)
                ->with(['mcqQuestions', 'creativeQuestions'])
                ->get();

            // Get all submitted answers with student details
            $answers = Answer::where('examination_id', $examId)
                ->where('is_answer_submitted', true)
                ->with(['student:id,name,email,phone,profile_image,section_id,active_status'])
                ->get();

            if ($answers->isEmpty()) {
                return ApiResponseHelper::error('No submissions found for this exam.', 404);
            }

            // Format response data
            $responseData = [
                'model_test' => [
                    'id' => $modelTest->id,
                    'title' => $modelTest->title,
                    'description' => $modelTest->description,
                    'start_time' => $modelTest->start_time,
                    'end_time' => $modelTest->end_time,
                    'is_active' => $modelTest->is_active,
                    'pass_mark' => $modelTest->pass_mark,
                    'full_mark' => $modelTest->full_mark
                ],
                'examination' => [
                    'id' => $examination->id,
                    'title' => $examination->title,
                    'description' => $examination->description,
                    'type' => $examination->type,
                    'start_time' => $examination->start_time,
                    'end_time' => $examination->end_time,
                    'student_ended_at' => $examination->student_ended_at,
                    'time_limit' => $examination->time_limit,
                    'is_negative_mark_applicable' => $examination->is_negative_mark_applicable,
                    'is_optional' => $examination->is_optional,
                    'is_active' => $examination->is_active,
                    'is_reviewed' => $examination->is_reviewed,
                    'section_id' => $examination->section_id,
                    'exam_type_id' => $examination->exam_type_id,
                    'exam_sub_type_id' => $examination->exam_sub_type_id,
                    'subject_id' => $examination->subject_id,
                    'lesson_id' => $examination->lesson_id,
                    'topic_id' => $examination->topic_id,
                    'sub_topic_id' => $examination->sub_topic_id,
                    'total_questions' => count($questionIds),
                    'total_submissions' => $answers->count()
                ],
                'submissions' => []
            ];

            foreach ($answers as $answer) {
                $submissionData = [
                    'answer_id' => $answer->id,
                    'student' => [
                        'id' => $answer->student->id,
                        'name' => $answer->student->name,
                        'email' => $answer->student->email,
                        'phone' => $answer->student->phone,
                        'profile_image' => $answer->student->profile_image,
                        'section_id' => $answer->student->section_id,
                        'active_status' => $answer->student->active_status
                    ],
                    'submission_details' => [
                        'exam_start_time' => $answer->exam_start_time,
                        'submission_time' => $answer->submission_time,
                        'is_exam_time_out' => $answer->is_exam_time_out,
                        'total_marks' => $answer->total_marks,
                        'correct_count' => $answer->correct_count,
                        'total_questions_count' => $answer->total_questions_count,
                        'status' => $answer->status,
                        'comments' => $answer->comments,
                        'is_reviewed' => $answer->is_reviewed
                    ],
                    'questions' => $questions->map(function ($question) use ($answer, $examination) {
                        $questionData = [
                            'id' => $question->id,
                            'title' => $question->title,
                            'description' => $question->description,
                            'type' => $question->type,
                            'mark' => $question->mark,
                            'images' => $question->images,
                            'tags' => $question->tags,
                            'is_paid' => $question->is_paid,
                            'is_featured' => $question->is_featured,
                            'status' => $question->status
                        ];

                        if ($examination->type === 'mcq') {
                            $questionData['mcq_options'] = $question->mcqQuestions->map(function ($option) {
                                return [
                                    'id' => $option->id,
                                    'mcq_option_serial' => $option->mcq_option_serial,
                                    'mcq_option_text' => $option->mcq_option_text,
                                    'is_correct' => $option->is_correct
                                ];
                            });

                            $submittedAnswer = collect($answer->mcq_answers)
                                ->firstWhere('question_id', $question->id);
                            if ($submittedAnswer) {
                                $questionData['student_answer'] = [
                                    'submitted_option' => $submittedAnswer['submitted_mcq_option'],
                                    'is_correct' => $submittedAnswer['is_submitted_correct'],
                                    'correct_option' => $submittedAnswer['correct_option_serial']
                                ];
                            }
                        } elseif ($examination->type === 'creative') {
                            $questionData['creative_parts'] = $question->creativeQuestions;
                            if (!empty($answer->creative_answers)) {
                                $questionData['student_answer'] = $answer->creative_answers;
                            }
                        } elseif ($examination->type === 'normal') {
                            if (!empty($answer->normal_answers)) {
                                $questionData['student_answer'] = $answer->normal_answers;
                            }
                        }

                        return $questionData;
                    })
                ];

                // Add file information for creative/normal type
                if ($examination->type !== 'mcq') {
                    $file = MTAnswerFile::where('student_id', $answer->student_id)
                        ->where('exam_id', $examId)
                        ->first();

                    if ($file) {
                        $submissionData['submitted_file'] = [
                            'file_url' => $file->file_url,
                            'cdn_url' => config('filesystems.disks.public.cdn_url', config('app.url').'/storage') . '/' .
                                ltrim(str_replace('/storage/', '', $file->file_url), '/'),
                            'original_filename' => $file->original_filename,
                            'uploaded_at' => $file->created_at
                        ];
                    }
                }

                $responseData['submissions'][] = $submissionData;
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
            // Get model test details
            $modelTest = ModelTest::findOrFail($mtId);

            // Get examination
            $examination = Examination::where('id', $examId)
                ->where('model_test_id', $mtId)
                ->firstOrFail();

            // Get questions with their options
            $questionIds = is_string($examination->questions) ?
                explode(',', $examination->questions) :
                $examination->questions;

            $questions = Question::whereIn('id', $questionIds)
                ->with(['mcqQuestions', 'creativeQuestions'])
                ->get();

            // Get student's answer
            $answer = Answer::where('examination_id', $examId)
                ->where('student_id', $studentId)
                ->where('is_answer_submitted', true)
                ->with(['student:id,name,email,phone,profile_image,section_id,active_status'])
                ->firstOrFail();

            $responseData = [
                'model_test' => [
                    'id' => $modelTest->id,
                    'title' => $modelTest->title,
                    'description' => $modelTest->description,
                    'start_time' => $modelTest->start_time,
                    'end_time' => $modelTest->end_time,
                    'is_active' => $modelTest->is_active,
                    'pass_mark' => $modelTest->pass_mark,
                    'full_mark' => $modelTest->full_mark
                ],
                'examination' => [
                    'id' => $examination->id,
                    'title' => $examination->title,
                    'description' => $examination->description,
                    'type' => $examination->type,
                    'start_time' => $examination->start_time,
                    'end_time' => $examination->end_time,
                    'student_ended_at' => $examination->student_ended_at,
                    'time_limit' => $examination->time_limit,
                    'is_negative_mark_applicable' => $examination->is_negative_mark_applicable,
                    'is_optional' => $examination->is_optional,
                    'is_active' => $examination->is_active,
                    'is_reviewed' => $examination->is_reviewed,
                    'section_id' => $examination->section_id,
                    'exam_type_id' => $examination->exam_type_id,
                    'exam_sub_type_id' => $examination->exam_sub_type_id,
                    'subject_id' => $examination->subject_id,
                    'lesson_id' => $examination->lesson_id,
                    'topic_id' => $examination->topic_id,
                    'sub_topic_id' => $examination->sub_topic_id
                ],
                'student' => [
                    'id' => $answer->student->id,
                    'name' => $answer->student->name,
                    'email' => $answer->student->email,
                    'phone' => $answer->student->phone,
                    'profile_image' => $answer->student->profile_image,
                    'section_id' => $answer->student->section_id,
                    'active_status' => $answer->student->active_status
                ],
                'submission_details' => [
                    'exam_start_time' => $answer->exam_start_time,
                    'submission_time' => $answer->submission_time,
                    'is_exam_time_out' => $answer->is_exam_time_out,
                    'total_marks' => $answer->total_marks,
                    'correct_count' => $answer->correct_count,
                    'total_questions_count' => $answer->total_questions_count,
                    'status' => $answer->status,
                    'comments' => $answer->comments,
                    'is_reviewed' => $answer->is_reviewed
                ],
                'questions' => $questions->map(function ($question) use ($answer, $examination) {
                    $questionData = [
                        'id' => $question->id,
                        'title' => $question->title,
                        'description' => $question->description,
                        'type' => $question->type,
                        'mark' => $question->mark,
                        'images' => $question->images,
                        'tags' => $question->tags,
                        'is_paid' => $question->is_paid,
                        'is_featured' => $question->is_featured,
                        'status' => $question->status
                    ];

                    if ($examination->type === 'mcq') {
                        $questionData['mcq_options'] = $question->mcqQuestions->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'mcq_option_serial' => $option->mcq_option_serial,
                                'mcq_option_text' => $option->mcq_option_text,
                                'is_correct' => $option->is_correct
                            ];
                        });

                        $submittedAnswer = collect($answer->mcq_answers)
                            ->firstWhere('question_id', $question->id);
                        if ($submittedAnswer) {
                            $questionData['student_answer'] = [
                                'submitted_option' => $submittedAnswer['submitted_mcq_option'],
                                'is_correct' => $submittedAnswer['is_submitted_correct'],
                                'correct_option' => $submittedAnswer['correct_option_serial']
                            ];
                        }
                    } elseif ($examination->type === 'creative') {
                        $questionData['creative_parts'] = $question->creativeQuestions;
                        if (!empty($answer->creative_answers)) {
                            $questionData['student_answer'] = $answer->creative_answers;
                        }
                    } elseif ($examination->type === 'normal') {
                        if (!empty($answer->normal_answers)) {
                            $questionData['student_answer'] = $answer->normal_answers;
                        }
                    }

                    return $questionData;
                })
            ];

            // Add file information for creative/normal type
            if ($examination->type !== 'mcq') {
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


    public function updateAnswerReview($mtId, $examId, $studentId, Request $request)
{
    try {
        // Validation
        $validator = Validator::make($request->all(), [
            'total_marks' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        // Check if examination exists and belongs to the model test
        $examination = Examination::where('id', $examId)
            ->where('model_test_id', $mtId)
            ->first();

        if (!$examination) {
            return ApiResponseHelper::error('Examination not found.', 404);
        }

        // Get the answer record
        $answer = Answer::where('examination_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if (!$answer) {
            return ApiResponseHelper::error('Answer record not found.', 404);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Update answer record
            $updateData = [];

            if ($request->has('total_marks')) {
                $updateData['total_marks'] = $request->total_marks;
                $updateData['is_reviewed'] = true;
            }

            if ($request->has('comments')) {
                $updateData['comments'] = $request->comments;
            }

            if (!empty($updateData)) {
                $answer->update($updateData);

                // Check if all answers for this exam are reviewed
                $totalAnswers = Answer::where('examination_id', $examId)
                    ->where('is_answer_submitted', true)
                    ->count();

                $reviewedAnswers = Answer::where('examination_id', $examId)
                    ->where('is_answer_submitted', true)
                    ->where('is_reviewed', true)
                    ->count();
            }

            DB::commit();

            return ApiResponseHelper::success([
                'answer' => [
                    'id' => $answer->id,
                    'total_marks' => $answer->total_marks,
                    'comments' => $answer->comments,
                    'is_reviewed' => $answer->is_reviewed
                ],
                'examination' => [
                    'id' => $examination->id,
                    'is_reviewed' => $examination->is_reviewed
                ]
            ], 'Answer review updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        Log::error('Error updating answer review', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'mt_id' => $mtId,
            'exam_id' => $examId,
            'student_id' => $studentId,
            'request' => $request->all()
        ]);

        return ApiResponseHelper::error(
            'An error occurred while updating the answer review.',
            500,
            ['details' => $e->getMessage()]
        );
    }
}

public function updateExaminationReviewStatus($mtId, $examId, Request $request)
{
    try {
        // Validation
        $validator = Validator::make($request->all(), [
            'is_reviewed' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        // Check if examination exists and belongs to the model test
        $examination = Examination::where('id', $examId)
            ->where('model_test_id', $mtId)
            ->first();

        if (!$examination) {
            return ApiResponseHelper::error('Examination not found.', 404);
        }

        // Update examination review status
        $examination->is_reviewed = $request->is_reviewed;
        $examination->save();

        return ApiResponseHelper::success([
            'examination' => [
                'id' => $examination->id,
                'title' => $examination->title,
                'is_reviewed' => $examination->is_reviewed
            ]
        ], 'Examination review status updated successfully');

    } catch (\Exception $e) {
        Log::error('Error updating examination review status', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'mt_id' => $mtId,
            'exam_id' => $examId,
            'request' => $request->all()
        ]);

        return ApiResponseHelper::error(
            'An error occurred while updating examination review status.',
            500,
            ['details' => $e->getMessage()]
        );
    }
}
}
