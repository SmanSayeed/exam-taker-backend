<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\DTOs\QuestionDTO\ExamSubTypeData;
use App\DTOs\QuestionDTO\ExamTypeData;
use App\DTOs\QuestionDTO\GroupData;
use App\DTOs\QuestionDTO\LessonData;
use App\DTOs\QuestionDTO\LevelData;
use App\DTOs\QuestionDTO\SubjectData;
use App\DTOs\QuestionDTO\SubTopicData;
use App\DTOs\QuestionDTO\TopicData;
use App\DTOs\QuestionDTO\YearData;
use App\Http\Controllers\Controller;
use App\DTOs\QuestionDTO\SectionData;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\Services\Question\QuestionBaseService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Exception;

class QuestionBaseController extends Controller
{
    protected QuestionBaseService $service;

    public function __construct(QuestionBaseService $service)
    {
        $this->service = $service;
    }

    // Maps resource types to models
    protected function getModel(string $resourceType): Model
    {
        $models = [
            'sections' => \App\Models\Section::class,
            'exam-types' => \App\Models\ExamType::class,
            'exam-sub-types' => \App\Models\ExamSubType::class,
            'years' => \App\Models\Year::class,
            'groups' => \App\Models\Group::class,
            'levels' => \App\Models\Level::class,
            'subjects' => \App\Models\Subject::class,
            'lessons' => \App\Models\Lesson::class,
            'topics' => \App\Models\Topic::class,
            'sub-topics' => \App\Models\SubTopic::class,
        ];

        if (!array_key_exists($resourceType, $models)) {
            throw new Exception('Invalid resource type.');
        }

        return new $models[$resourceType];
    }

    protected function getDtoFromRequest(QuestionBaseRequest $request, string $resourceType)
    {

        $dtoClass = $this->getDtoClass($resourceType);
        return $dtoClass::from($request->validated());
    }

    protected function getDtoClass(string $resourceType)
    {
        $dtoClasses = [
            'sections' => SectionData::class,
            'exam-types' => ExamTypeData::class,
            'exam-sub-types' => ExamSubTypeData::class,
            'years' => YearData::class,
            'groups' => GroupData::class,
            'levels' => LevelData::class,
            'subjects' => SubjectData::class,
            'lessons' => LessonData::class,
            'topics' => TopicData::class,
            'sub-topics' => SubTopicData::class,
        ];

        if (!array_key_exists($resourceType, $dtoClasses)) {
            throw new Exception('Invalid DTO class.');
        }

        return $dtoClasses[$resourceType];
    }

    public function getData(string $resourceType): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $items = $this->service->getAll();
            return ApiResponseHelper::success($items);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve items: ' . $e->getMessage());
        }
    }

    public function show(string $resourceType, int $id): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $item = $this->service->findById($id);
            return $item ? ApiResponseHelper::success($item) : ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve item: ' . $e->getMessage());
        }
    }

    public function store(QuestionBaseRequest $request, string $resourceType): JsonResponse
    {
        // dd($request->all());
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            // dd($request);
            try {
                $dto = $this->getDtoFromRequest($request, $resourceType);
            } catch (\Throwable $e) {
                // Log or debug the error
                dd('Error creating DTO:', $e->getMessage());
            }


            $item = $this->service->create($dto->toArray());
            return ApiResponseHelper::success($item, 'Item created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create item: ' . $e->getMessage());
        }
    }

    public function update(QuestionBaseRequest $request, string $resourceType, int $id): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $dto = $this->getDtoFromRequest($request, $resourceType);
            $updated = $this->service->update($id, $dto->toArray());
            return $updated ? ApiResponseHelper::success(null, 'Item updated successfully') : ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update item: ' . $e->getMessage());
        }
    }

    public function destroy(string $resourceType, int $id): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $deleted = $this->service->delete($id);
            return $deleted ? ApiResponseHelper::success(null, 'Item deleted successfully') : ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete item: ' . $e->getMessage());
        }
    }

    public function changeStatus(Request $request, string $resourceType, int $id): JsonResponse
    {
        try {
            $status = $request->input('status'); // Get status from request
            if (!is_bool($status)) {
                return ApiResponseHelper::error('Invalid status value. It must be a boolean.', 400);
            }

            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $item = $this->service->findById($id);
            if (!$item) {
                return ApiResponseHelper::error('Item not found', 404);
            }

            $item->status = $status;
            $item->save();

            return ApiResponseHelper::success(null, 'Status updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update status: ' . $e->getMessage());
        }
    }
}
