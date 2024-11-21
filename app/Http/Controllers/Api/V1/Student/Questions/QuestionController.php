<?php
namespace App\Http\Controllers\Api\V1\Student\Questions;

use App\DTOs\CreateQuestionDTO\CreativeQuestionData;
use App\DTOs\CreateQuestionDTO\McqQuestionData;
use App\DTOs\CreateQuestionDTO\NormalTextQuestionData;
use App\DTOs\CreateQuestionDTO\QuestionData;
use App\Http\Requests\Api\V1\Questions\QuestionRequest;
use App\Http\Requests\Api\V1\Questions\McqQuestionRequest;
use App\Http\Requests\Api\V1\Questions\NormalTextQuestionRequest;
use App\Http\Requests\Api\V1\Questions\CreativeQuestionRequest;
use App\Services\Question\QuestionService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    protected QuestionService $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function searchAndFilterQuestions(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'keyword' => 'nullable|string',
            'type' => 'nullable|array', // Allow an array of types
            'type.*' => 'string|in:mcq,creative,normal', // Validate each type in the array
            'section_id' => 'nullable|array',
            'section_id.*' => 'integer|exists:sections,id',
            'exam_type_id' => 'nullable|array',
            'exam_type_id.*' => 'integer|exists:exam_types,id',
            'exam_sub_type_id' => 'nullable|array',
            'exam_sub_type_id.*' => 'integer|exists:exam_sub_types,id',
            'group_id' => 'nullable|array',
            'group_id.*' => 'integer|exists:groups,id',
            'level_id' => 'nullable|array',
            'level_id.*' => 'integer|exists:levels,id',
            'subject_id' => 'nullable|array',
            'subject_id.*' => 'integer|exists:subjects,id',
            'lesson_id' => 'nullable|array',
            'lesson_id.*' => 'integer|exists:lessons,id',
            'topic_id' => 'nullable|array',
            'topic_id.*' => 'integer|exists:topics,id',
            'sub_topic_id' => 'nullable|array',
            'sub_topic_id.*' => 'integer|exists:sub_topics,id',
            'perPage' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        try {
            // Get the validated data
            $filters = $validator->validated();
            $keyword = $filters['keyword'] ?? '';
            $perPage = $filters['perPage'] ?? 10;

            // Call the service method
            $questions = $this->questionService->searchAndFilterQuestions($filters, $keyword, $perPage);

            // Return a successful response
            return ApiResponseHelper::success($questions, 'Questions retrieved successfully');
        } catch (Exception $e) {
            // Return an error response in case of an exception
            return ApiResponseHelper::error('Failed to retrieve questions', 500, ['error' => $e->getMessage()]);
        }
    }
}
