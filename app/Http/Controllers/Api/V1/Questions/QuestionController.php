<?php
namespace App\Http\Controllers\Api\V1\Questions;

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

    public function storeQuestion(QuestionRequest $request): JsonResponse
    {
        try {
            $dto = QuestionData::from($request->validated());
            $question = $this->questionService->createQuestion($dto);
            return ApiResponseHelper::success($question, 'Question created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function storeMcqQuestion(McqQuestionRequest $request): JsonResponse
    {
        try {
            $dto = McqQuestionData::from(array_merge($request->validated()));
            $mcqQuestion = $this->questionService->createMcqQuestion($dto);
            return ApiResponseHelper::success($mcqQuestion, 'MCQ question created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create MCQ question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function storeNormalTextQuestion(NormalTextQuestionRequest $request): JsonResponse
    {
        try {
            $dto = NormalTextQuestionData::from(array_merge($request->validated()));
            $normalTextQuestion = $this->questionService->createNormalTextQuestion($dto);
            return ApiResponseHelper::success($normalTextQuestion, 'Normal text question created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create normal text question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function storeCreativeQuestion(CreativeQuestionRequest $request): JsonResponse
    {

        try {
            $dto = CreativeQuestionData::from(array_merge($request->validated()));

            $creativeQuestion = $this->questionService->createCreativeQuestion($dto);
            return ApiResponseHelper::success($creativeQuestion, 'Creative question created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create creative question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function updateQuestion(int $id, QuestionRequest $request): JsonResponse
    {
        try {
            $dto = QuestionData::from($request->validated());
            $question = $this->questionService->updateQuestion($id, $dto);
            return ApiResponseHelper::success($question, 'Question updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function updateMcqQuestion(int $id, McqQuestionRequest $request): JsonResponse
    {
        try {
            $dto = McqQuestionData::from($request->validated());
            $mcqQuestion = $this->questionService->updateMcqQuestion($id, $dto);
            return ApiResponseHelper::success($mcqQuestion, 'MCQ question updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update MCQ question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function updateNormalTextQuestion(int $id, NormalTextQuestionRequest $request): JsonResponse
    {
        try {
            $dto = NormalTextQuestionData::from($request->validated());
            $normalTextQuestion = $this->questionService->updateNormalTextQuestion($id, $dto);
            return ApiResponseHelper::success($normalTextQuestion, 'Normal text question updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update normal text question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function updateCreativeQuestion(int $id, CreativeQuestionRequest $request): JsonResponse
    {
        try {
            $dto = CreativeQuestionData::from($request->validated());
            $creativeQuestion = $this->questionService->updateCreativeQuestion($id, $dto);
            return ApiResponseHelper::success($creativeQuestion, 'Creative question updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update creative question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function changeQuestionStatus(int $id): JsonResponse
    {
        try {
            $question = $this->questionService->changeStatus($id);
            return ApiResponseHelper::success($question, 'Question status updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to change question status', 500, ['error' => $e->getMessage()]);
        }
    }

    // Retrieve all questions

    public function getAllQuestions(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $perPage = $request->query('perPage', 10);

        try {
            $questions = $this->questionService->getQuestionsByType($type, $perPage);
            return ApiResponseHelper::success($questions, 'Questions retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve questions', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getQuestion(int $id): JsonResponse
    {
        try {
            $question = $this->questionService->getQuestion($id);
            return ApiResponseHelper::success($question, 'Question retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function deleteQuestion(int $id): JsonResponse
    {
        try {
            $this->questionService->deleteQuestion($id);
            return ApiResponseHelper::success(null, 'Question deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function deleteMcqQuestion(int $id): JsonResponse
    {
        try {
            $this->questionService->deleteMcqQuestion($id);
            return ApiResponseHelper::success(null, 'MCQ question deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete MCQ question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function deleteNormalTextQuestion(int $id): JsonResponse
    {
        try {
            $this->questionService->deleteNormalTextQuestion($id);
            return ApiResponseHelper::success(null, 'Normal text question deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete normal text question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function deleteCreativeQuestion(int $id): JsonResponse
    {
        try {
            $this->questionService->deleteCreativeQuestion($id);
            return ApiResponseHelper::success(null, 'Creative question deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete creative question', 500, ['error' => $e->getMessage()]);
        }
    }


    public function getAllMcqQuestions(int $perPage = 10): JsonResponse
    {
        try {
            $mcqQuestions = $this->questionService->getAllMcqQuestions($perPage);
            return ApiResponseHelper::success($mcqQuestions, 'MCQ questions retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve MCQ questions', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getAllCreativeQuestions(int $perPage = 10): JsonResponse
    {
        try {
            $creativeQuestions = $this->questionService->getAllCreativeQuestions($perPage);
            return ApiResponseHelper::success($creativeQuestions, 'Creative questions retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve creative questions', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getMcqQuestion(int $id): JsonResponse
    {
        try {
            $mcqQuestion = $this->questionService->getMcqQuestion($id);
            return ApiResponseHelper::success($mcqQuestion, 'MCQ question retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve MCQ question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function getCreativeQuestion(int $id): JsonResponse
    {
        try {
            $mcqQuestion = $this->questionService->getCreativeQuestion($id);
            return ApiResponseHelper::success($mcqQuestion, 'Creative question retrieved successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve MCQ question', 500, ['error' => $e->getMessage()]);
        }
    }

    public function searchByKeywordAndType(Request $request): JsonResponse
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'keyword' => 'nullable|string',
        'types' => 'nullable|array',
        'types.*' => 'in:mcq,creative,normal', // Validate that each type is one of the allowed values
        'perPage' => 'nullable|integer|min:1', // Optional pagination parameter
    ]);

    if ($validator->fails()) {
        return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
    }

    try {
        // Get the validated data
        $filters = $validator->validated();
        $keyword = $filters['keyword'] ?? ''; // Default to an empty string if keyword is not provided
        $types = $filters['types'] ?? []; // Default to an empty array if types are not provided
        $perPage = $filters['perPage'] ?? 10; // Default to 10 if perPage is not provided

        // Call the service method
        $questions = $this->questionService->searchByKeywordAndType($types, $keyword, $perPage);

        // Return a successful response
        return ApiResponseHelper::success($questions, 'Questions retrieved successfully');
    } catch (Exception $e) {
        // Return an error response in case of an exception
        return ApiResponseHelper::error('Failed to retrieve questions', 500, ['error' => $e->getMessage()]);
    }
}



    public function searchAndFilterQuestions(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'keyword' => 'nullable|string',
            'type' => 'nullable|string|in:mcq,creative,normal',
            'section_id' => 'nullable|integer|exists:sections,id',
            'exam_type_id' => 'nullable|integer|exists:exam_types,id',
            'exam_sub_type_id' => 'nullable|integer|exists:exam_sub_types,id',
            'group_id' => 'nullable|integer|exists:groups,id',
            'level_id' => 'nullable|integer|exists:levels,id',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'topic_id' => 'nullable|integer|exists:topics,id',
            'sub_topic_id' => 'nullable|integer|exists:sub_topics,id',
            'perPage' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        try {
            // Get the validated data
            $filters = $validator->validated();
            $keyword = $filters['keyword'] ?? ''; // Default to an empty string if keyword is not provided
            $perPage = $filters['perPage'] ?? 10; // Default to 10 if perPage is not provided

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
