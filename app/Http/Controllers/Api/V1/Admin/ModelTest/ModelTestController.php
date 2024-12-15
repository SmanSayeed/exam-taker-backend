<?php

namespace App\Http\Controllers\Api\V1\Admin\ModelTest;

use App\Http\Requests\Admin\ModelTest\StoreModelTestRequest;
use App\Http\Requests\Admin\ModelTest\UpdateModelTestRequest;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModelTest\UpdateModelTestStatusRequest;
use App\Http\Requests\AttachExaminationsRequest;
use App\Http\Requests\AttachPdfRequest;
use App\Http\Requests\DetachExaminationsRequest;
use App\Http\Requests\DetachPdfRequest;
use App\Http\Requests\ModelTestIndexRequest;
use App\Http\Resources\ExaminationResource;
use App\Http\Resources\ModelTestResource;
use App\Http\Resources\ModelTestWithQuestionsResource;
use App\Models\ModelTest;
use App\Models\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ModelTestController extends Controller
{
    public function index(ModelTestIndexRequest $request): JsonResponse
    {
        // Get the 'per_page' parameter from the request, defaulting to 15 if not provided
        $perPage = $request->get('per_page', 15);

        // Start the query for ModelTest, eager loading the related modelTestCategory
        $query = ModelTest::with('modelTestCategory');

        // Apply filters based on request parameters if they are present
        if ($request->has('group_id')) {
            $query->whereHas('modelTestCategory', function ($q) use ($request) {
                $q->where('group_id', $request->input('group_id'));
            });
        }

        if ($request->has('level_id')) {
            $query->whereHas('modelTestCategory', function ($q) use ($request) {
                $q->where('level_id', $request->input('level_id'));
            });
        }

        if ($request->has('subject_id')) {
            $query->whereHas('modelTestCategory', function ($q) use ($request) {
                $q->where('subject_id', $request->input('subject_id'));
            });
        }

        if ($request->has('lesson_id')) {
            $query->whereHas('modelTestCategory', function ($q) use ($request) {
                $q->where('lesson_id', $request->input('lesson_id'));
            });
        }

        if ($request->has('topic_id')) {
            $query->whereHas('modelTestCategory', function ($q) use ($request) {
                $q->where('topic_id', $request->input('topic_id'));
            });
        }

        if ($request->has('sub_topic_id')) {
            $query->whereHas('modelTestCategory', function ($q) use ($request) {
                $q->where('sub_topic_id', $request->input('sub_topic_id'));
            });
        }

        // Execute the query with pagination
        $modelTests = $query->paginate($perPage);

        // Return a successful response with the paginated results
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

            $modelTest->modelTestCategory()->create($request->category);

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
            // Get the list of examination IDs from the request
            $examinationIds = $request->input('examination_ids'); // Assume this is an array of examination IDs

            // Attach the examinations to the model test using the pivot table
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
            // Get the list of examination IDs from the request
            $examinationIds = $request->input('examination_ids'); // Assume this is an array of examination IDs

            // Detach the examinations from the model test
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
            // Get all examinations associated with the model test
            $examinations = $modelTest->examinations;

            // Check if there are no examinations
            if ($examinations->isEmpty()) {
                return ApiResponseHelper::success(null, 'No examinations found for this model test');
            }

            // Return the examinations using the ExaminationResource
            return ApiResponseHelper::success(ExaminationResource::collection($examinations), 'Examinations retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve examinations', 500, $e->getMessage());
        }
    }
}
