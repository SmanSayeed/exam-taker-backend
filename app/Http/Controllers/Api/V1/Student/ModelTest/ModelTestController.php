<?php

namespace App\Http\Controllers\Api\V1\Student\ModelTest;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModelTestIndexRequest;
use App\Http\Resources\ModelTestResource;
use App\Models\ModelTest;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ModelTestController extends Controller
{
    public function index(Package $package): JsonResponse
    {
        $student = Auth::guard('student-api')->user();

        // Check if the student is subscribed to the package
        $isSubscribed = $student->subscriptions()->where('package_id', $package->id)->exists();

        if (!$isSubscribed) {
            return ApiResponseHelper::error('You are not subscribed to this package.', 403);
        }

        // Check if the package is active
        if (!$package->is_active) {
            return ApiResponseHelper::error('Package is not active.', 404);
        }

        // Fetch only active model tests associated with the active package
        $modelTests = $package->modelTests()->active()->get();

        // Return the filtered model tests
        return ApiResponseHelper::success(
            ModelTestResource::collection($modelTests),
            'Active model tests for subscribed package retrieved successfully'
        );
    }

    // Show a specific model test by ID
    public function show(ModelTest $modelTest): JsonResponse
    {
        $student = Auth::guard('student-api')->user();

        if (!$modelTest->is_active) {
            return ApiResponseHelper::error('Model test is not active.', 404);
        }

        $package = $modelTest->package;

        if (!$package->is_active) {
            return ApiResponseHelper::error('Package is not active.', 404);
        }

        $isSubscribed = $student->subscriptions()->where('package_id', $package->id)->exists();

        if (!$isSubscribed) {
            return ApiResponseHelper::error('You are not subscribed to this package.', 403);
        }   

        // Return the model test resource
        return ApiResponseHelper::success(
            new ModelTestResource($modelTest),
            'Model test retrieved successfully'
        );
    }
}
