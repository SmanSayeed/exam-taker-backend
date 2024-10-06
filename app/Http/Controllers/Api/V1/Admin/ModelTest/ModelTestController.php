<?php

namespace App\Http\Controllers\Api\V1\Admin\ModelTest;

use App\Http\Requests\Admin\ModelTest\StoreModelTestRequest;
use App\Http\Requests\Admin\ModelTest\UpdateModelTestRequest;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModelTest\UpdateModelTestStatusRequest;
use App\Http\Resources\ModelTestResource;
use App\Models\ModelTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModelTestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $modelTests = ModelTest::with('category')
            ->paginate($perPage);

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
            if ($request->has('category')) {
                $modelTest->modelTestCategory()->create($request->category);
            }

            DB::commit();

            $modelTest->load([
                'package',
                'modelTestCategory.section',
                'modelTestCategory.examType',
                'modelTestCategory.examSubType',
                'modelTestCategory.group',
                'modelTestCategory.level',
                'modelTestCategory.subject',
                'modelTestCategory.lesson',
                'modelTestCategory.topic',
                'modelTestCategory.subTopic'
            ]);

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
            // Update the model test attributes
            $modelTest->update($request->validated());

            // If category data is provided in the request, update the related category
            if ($request->has('category')) {
                if ($modelTest->modelTestCategory) {
                    // Update the existing category
                    $modelTest->modelTestCategory()->update($request->category);
                } else {
                    // Create a new category if none exists
                    $modelTest->modelTestCategory()->create($request->category);
                }
            }

            // Reload the related relationships
            $modelTest->load([
                'package',
                'modelTestCategory.section',
                'modelTestCategory.examType',
                'modelTestCategory.examSubType',
                'modelTestCategory.group',
                'modelTestCategory.level',
                'modelTestCategory.subject',
                'modelTestCategory.lesson',
                'modelTestCategory.topic',
                'modelTestCategory.subTopic'
            ]);

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
            $modelTest->category()->delete();
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
            new ModelTestResource($modelTest->load('category')),
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
}
