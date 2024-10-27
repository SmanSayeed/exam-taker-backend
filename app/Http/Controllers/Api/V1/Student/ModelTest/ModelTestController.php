<?php

namespace App\Http\Controllers\Api\V1\Student\ModelTest;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModelTestIndexRequest;
use App\Http\Resources\ModelTestResource;
use App\Models\ModelTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function show(ModelTest $modelTest): JsonResponse
    {
        return ApiResponseHelper::success(
            new ModelTestResource($modelTest),
            'Model test retrieved successfully'
        );
    }
}
