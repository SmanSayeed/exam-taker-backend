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
use App\Http\Requests\Api\V1\Questions\QuestionCategoryRequest;
use App\Services\Question\QuestionCategoryService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Exception;

class QuestionCategoryController extends Controller
{
    protected QuestionCategoryService $service;

    public function __construct(QuestionCategoryService $service)
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

    protected function getDtoFromRequest(QuestionCategoryRequest $request, string $resourceType)
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

      // Add this method to specify relations to load based on the resource type
      protected function getRelations(string $resourceType): array
      {
          $relations = [
              'sections' => ['examTypes'],
              'exam-types' => ['section', 'examSubTypes'],
              'exam-sub-types' => ['examType'],
              'years' => [], // No direct relationships defined in the models for years
              'groups' => ['levels', 'subjects'],
              'levels' => ['subjects', 'group'],
              'subjects' => ['lessons', 'level', 'group'],
              'lessons' => ['subject', 'topics'],
              'topics' => ['subTopics', 'lesson'],
              'sub-topics' => ['topic'],
          ];

          return $relations[$resourceType] ?? [];
      }


    public function getData(string $resourceType): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);
            // Specify relations to load
            $relations = $this->getRelations($resourceType);
            $items = $this->service->getAll($relations);
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
            $relations = $this->getRelations($resourceType);
            $item = $this->service->findById($id,$relations);
            return $item ? ApiResponseHelper::success($item) : ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve item: ' . $e->getMessage());
        }
    }

    public function store(QuestionCategoryRequest $request, string $resourceType): JsonResponse
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
            return ApiResponseHelper::success($item, $resourceType.' created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create '.$resourceType.': ' . $e->getMessage());
        }
    }

    public function update(QuestionCategoryRequest $request, string $resourceType, int $id): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $dto = $this->getDtoFromRequest($request, $resourceType);
            $updated = $this->service->update($id, $dto->toArray());
            return $updated ? ApiResponseHelper::success(null, $resourceType.' updated successfully') : ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update '.$resourceType.': ' . $e->getMessage());
        }
    }


    public function destroy(string $resourceType, int $id): JsonResponse
    {
        try {
            $model = $this->getModel($resourceType);
            $this->service->setModel($model);

            $deleted = $this->service->delete($id);
            return $deleted ? ApiResponseHelper::success(null, 'Item deleted successfully') : ApiResponseHelper::error('Item not found', 404);
        } catch (QueryException $e) {
            // Handle integrity constraint violation
            if ($e->getCode() == 23000) { // SQLSTATE[23000] is for integrity constraint violations
                $errorMessage = $this->getConstraintViolationMessage($e->getMessage());
                return ApiResponseHelper::error($errorMessage, 400);
            }
            return ApiResponseHelper::error('Failed to delete item: Database error.', 500);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Parse the QueryException message to return a specific error message
     * indicating the related table causing the integrity constraint violation.
     */
    private function getConstraintViolationMessage(string $exceptionMessage): string
    {
        if (strpos($exceptionMessage, 'questionables_section_id_foreign') !== false) {
            return 'Failed to delete section: This section has related items in `exam_types`. Please remove or update the related records before deleting this section.';
        }
        if (strpos($exceptionMessage, 'exam_types_section_id_foreign') !== false) {
            return 'Failed to delete section: This section has related items in `exam_types`.';
        }
        if (strpos($exceptionMessage, 'exam_sub_types_exam_type_id_foreign') !== false) {
            return 'Failed to delete exam type: This exam type has related items in `exam_sub_types`.';
        }
        if (strpos($exceptionMessage, 'levels_group_id_foreign') !== false) {
            return 'Failed to delete group: This group has related items in `levels`.';
        }
        if (strpos($exceptionMessage, 'subjects_level_id_foreign') !== false) {
            return 'Failed to delete level: This level has related items in `subjects`.';
        }
        if (strpos($exceptionMessage, 'subjects_group_id_foreign') !== false) {
            return 'Failed to delete group: This group has related items in `subjects`.';
        }
        if (strpos($exceptionMessage, 'lessons_subject_id_foreign') !== false) {
            return 'Failed to delete subject: This subject has related items in `lessons`.';
        }
        if (strpos($exceptionMessage, 'topics_lesson_id_foreign') !== false) {
            return 'Failed to delete lesson: This lesson has related items in `topics`.';
        }
        if (strpos($exceptionMessage, 'sub_topics_topic_id_foreign') !== false) {
            return 'Failed to delete topic: This topic has related items in `sub_topics`.';
        }

        // Add more checks for other foreign key constraints as needed
        return 'Failed to delete item: Integrity constraint violation. This item may be referenced by other records.';
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
