<?php

namespace App\Http\Controllers\Api\V1\Student\ModelTest;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModelTestResource;
use App\Models\ModelTest;
use App\Models\Package;
use Illuminate\Http\JsonResponse;

class ModelTestController extends Controller
{
    public function index(Package $package): JsonResponse
    {
        // Check if the package is active
        if (!$package->is_active) {
            return ApiResponseHelper::error('Package is not active.', 404);
        }

        // Fetch only active model tests associated with the active package
        $modelTests = $package->modelTests()->active()->get();

        // Return the filtered model tests
        return ApiResponseHelper::success(
            ModelTestResource::collection($modelTests),
            'Active model tests for the package retrieved successfully'
        );
    }

    // Show a specific model test by ID
    public function show(ModelTest $modelTest): JsonResponse
    {
        if (!$modelTest->is_active) {
            return ApiResponseHelper::error('Model test is not active.', 404);
        }

        $package = $modelTest->package;

        if (!$package->is_active) {
            return ApiResponseHelper::error('Package is not active.', 404);
        }

        // Return the model test resource
        return ApiResponseHelper::success(
            new ModelTestResource($modelTest),
            'Model test retrieved successfully'
        );
    }
}
