<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\Http\Controllers\Controller;
use App\Services\Question\StudentQuestionCategoryService;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Exception;

class StudentQuestionCategoryController extends Controller
{
    protected StudentQuestionCategoryService $service;

    /**
     * Inject the StudentQuestionCategoryService via the constructor
     *
     * @param StudentQuestionCategoryService $service
     */

    public function __construct(StudentQuestionCategoryService $service)
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












}
