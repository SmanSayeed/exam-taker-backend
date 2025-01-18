<?php

namespace App\Http\Controllers\Examination;

use App\Models\MTAnswerFile;
use App\Models\Examination;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class MTAnswerFileController extends Controller
{
    private function generateCdnUrl($filePath)
    {
        $baseUrl = config('filesystems.disks.public.cdn_url', config('app.url').'/storage');
        return $baseUrl . '/' . ltrim($filePath, '/');
    }

    public function upload(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'exam_id' => 'required|exists:examinations,id',
                'answer_file' => [
                    'required',
                    'file',
                    'mimes:pdf,docx',
                    'max:10240', // 10MB in kilobytes
                ]
            ]);

            if ($validator->fails()) {
                return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
            }

            // Check if examination exists and is active
            $examination = Examination::find($request->exam_id);
            if (!$examination || !$examination->is_active) {
                return ApiResponseHelper::error('Examination not found or inactive', 404);
            }

            // Check exam time
            $now = Carbon::now();
            // if ($now->lt($examination->start_time)) {
            //     return ApiResponseHelper::error('The exam has not started yet.', 403);
            // }
            // if ($now->gt($examination->end_time)) {
            //     return ApiResponseHelper::error('The exam has already ended.', 403);
            // }

            // Check if answer exists and is submitted
            $answer = Answer::where('examination_id', $request->exam_id)
                          ->where('student_id', $request->student_id)
                          ->first();

            if (!$answer) {
                return ApiResponseHelper::error('No exam attempt found for this student.', 404);
            }

            if ($answer->is_answer_submitted) {
                return ApiResponseHelper::error('Answer has already been submitted. Cannot upload new file.', 403);
            }

            $file = $request->file('answer_file');

            // Check if student already uploaded a file
            $existingFile = MTAnswerFile::where('exam_id', $request->exam_id)
                                      ->where('student_id', $request->student_id)
                                      ->first();

            // Generate unique filename
            $fileName = sprintf(
                '%s_%s_%s_%s.%s',
                $examination->type,
                $request->student_id,
                $request->exam_id,
                now()->format('Y_m_d_His'),
                $file->getClientOriginalExtension()
            );

            // Store new file
            $filePath = $file->storeAs('answer_files', $fileName, 'public');
            $fileUrl = Storage::url($filePath);
            $cdnUrl = $this->generateCdnUrl($filePath);

            // Delete old file if exists
            if ($existingFile) {
                // Delete the old file from storage
                Storage::disk('public')->delete(str_replace('/storage/', '', $existingFile->file_url));
                // Delete old record
                $existingFile->delete();
            }

            // Create new record
            $answerFile = MTAnswerFile::create([
                'student_id' => $request->student_id,
                'exam_id' => $request->exam_id,
                'file_url' => $fileUrl,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize()
            ]);

            // Add CDN URL to the response
            $responseData = $answerFile->toArray();
            $responseData['cdn_url'] = $cdnUrl;

            $successMessage = $existingFile
                ? 'File replaced successfully'
                : 'File uploaded successfully';

            return ApiResponseHelper::success(
                ['file' => $responseData],
                $successMessage
            );

        } catch (\Exception $e) {
            Log::error('Error uploading answer file', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return ApiResponseHelper::error(
                'An error occurred while uploading the file',
                500,
                ['details' => $e->getMessage()]
            );
        }
    }

    public function getFilesByExam($examId, Request $request)
    {
        try {
            // Validation
            $validator = Validator::make([
                'exam_id' => $examId,
                'student_id' => $request->student_id
            ], [
                'exam_id' => 'required|exists:examinations,id',
                'student_id' => 'required|exists:students,id'
            ]);

            if ($validator->fails()) {
                return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
            }

            // Get the file with related exam and student details
            $file = MTAnswerFile::where('exam_id', $examId)
                ->where('student_id', $request->student_id)
                ->with(['student', 'examination'])
                ->first();

            if (!$file) {
                return ApiResponseHelper::error('No file found for this student and exam.', 404);
            }

            // Transform the response
            $responseData = [
                'file' => [
                    'id' => $file->id,
                    'file_url' => $file->file_url,
                    'cdn_url' => $this->generateCdnUrl(str_replace('/storage/', '', $file->file_url)),
                    'original_filename' => $file->original_filename,
                    'mime_type' => $file->mime_type,
                    'file_size' => $file->file_size,
                    'created_at' => $file->created_at,
                    'updated_at' => $file->updated_at
                ],
                'examination' => $file->examination,
                'student' => $file->student
            ];

            return ApiResponseHelper::success(
                $responseData,
                'File details retrieved successfully'
            );

        } catch (\Exception $e) {
            Log::error('Error retrieving answer file', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'exam_id' => $examId,
                'student_id' => $request->student_id
            ]);

            return ApiResponseHelper::error(
                'An error occurred while retrieving the file',
                500,
                ['details' => $e->getMessage()]
            );
        }
    }
}
