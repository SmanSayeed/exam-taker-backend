<?php

namespace App\Http\Controllers\Api\V1\Admin\ModelTest;

use App\Http\Requests\Admin\ModelTest\StoreModelTestRequest;
use App\Http\Requests\Admin\ModelTest\UpdateModelTestRequest;
use App\Http\Requests\Admin\ModelTest\UpdateModelTestStatusRequest;
use App\Http\Requests\AttachExaminationsRequest;
use App\Http\Requests\DetachExaminationsRequest;
use App\Http\Requests\ModelTestIndexRequest;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExaminationResource;
use App\Http\Resources\ModelTestResource;
use App\Models\ModelTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ModelTestController extends Controller
{
    public function index(ModelTestIndexRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', 1500000);
        $query = ModelTest::with('modelTestCategory');

        // Dynamically apply filters for category fields if provided
        $filters = ['group_id', 'level_id', 'subject_id', 'lesson_id', 'topic_id', 'sub_topic_id'];
        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                $query->whereHas('modelTestCategory', function ($q) use ($filter, $request) {
                    $q->where($filter, $request->input($filter));
                });
            }
        }

        // Add orderBy clause for created_at in descending order
        $modelTests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return ApiResponseHelper::success(
            ModelTestResource::collection($modelTests),
            'Model tests retrieved successfully'
        );
    }

    public function store(StoreModelTestRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $modelTest = ModelTest::create($request->validated());

            // Create category if provided
            if ($request->has('category') && !empty($request->category)) {
                $modelTest->modelTestCategory()->create($request->category);
            }

            DB::commit();

            return ApiResponseHelper::success(
                new ModelTestResource($modelTest),
                'Model test created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to create model test', 500, $e->getMessage());
        }
    }

    public function update(UpdateModelTestRequest $request, ModelTest $modelTest): JsonResponse
    {
        DB::beginTransaction();
        try {
            $modelTest->update($request->validated());

            // Update or create category if provided
            if ($request->has('category')) {
                if ($modelTest->modelTestCategory) {
                    $modelTest->modelTestCategory()->update($request->category);
                } elseif (!empty($request->category)) {
                    $modelTest->modelTestCategory()->create($request->category);
                }
            }

            DB::commit();

            return ApiResponseHelper::success(
                new ModelTestResource($modelTest),
                'Model test updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to update model test', 500, $e->getMessage());
        }
    }

    public function destroy(ModelTest $modelTest): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Check if the model test has a category and delete it if it exists
            if ($modelTest->modelTestCategory) {
                $modelTest->modelTestCategory()->delete();
            }

            $modelTest->delete();

            DB::commit();

            return ApiResponseHelper::success('Model test deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to delete model test', 500, $e->getMessage());
        }
    }

    public function show(ModelTest $modelTest): JsonResponse
    {
        return ApiResponseHelper::success(
            new ModelTestResource($modelTest),
            'Model test retrieved successfully'
        );
    }

    public function changeStatus(UpdateModelTestStatusRequest $request, ModelTest $modelTest): JsonResponse
    {
        try {
            $modelTest->update(['is_active' => $request->is_active]);
            return ApiResponseHelper::success(
                new ModelTestResource($modelTest),
                'Model test status changed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to change model test status', 500, $e->getMessage());
        }
    }

    public function attachExamination(AttachExaminationsRequest $request, ModelTest $modelTest): JsonResponse
    {
        DB::beginTransaction();
        try {
            $examinationIds = $request->input('examination_ids');
            $modelTest->examinations()->attach($examinationIds);

            DB::commit();

            return ApiResponseHelper::success(null, 'Examinations added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to attach examinations', 500, $e->getMessage());
        }
    }

    public function detachExamination(DetachExaminationsRequest $request, ModelTest $modelTest): JsonResponse
    {
        DB::beginTransaction();
        try {
            $examinationIds = $request->input('examination_ids');
            $modelTest->examinations()->detach($examinationIds);

            DB::commit();

            return ApiResponseHelper::success(null, 'Examinations detached successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to detach examinations', 500, $e->getMessage());
        }
    }

    public function getExaminations(ModelTest $modelTest): JsonResponse
    {
        try {
            $examinations = $modelTest->examinations;

            if ($examinations->isEmpty()) {
                return ApiResponseHelper::success(null, 'No examinations found for this model test');
            }

            return ApiResponseHelper::success(
                ExaminationResource::collection($examinations),
                'Examinations retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve examinations', 500, $e->getMessage());
        }
    }
}
