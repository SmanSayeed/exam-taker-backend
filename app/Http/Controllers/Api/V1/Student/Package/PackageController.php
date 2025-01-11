<?php

namespace App\Http\Controllers\Api\V1\Student\Package;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageIndexRequest;
use App\Http\Resources\PackageStudentResource;
use App\Models\Package;
use App\Models\PackageCategory;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    public function index(PackageIndexRequest $request): JsonResponse
    {
        // Get per_page value from request or set default to 15
        $perPage = $request->get('per_page', 15);

        // Initialize the query
        $query = Package::query();

        // Check for filtering parameters
        if ($request->has('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }
        if ($request->has('exam_type_id')) {
            $query->where('exam_type_id', $request->input('exam_type_id'));
        }

        // Paginate the results
        $packages = $query->active()->paginate($perPage);

        return ApiResponseHelper::success(
            PackageStudentResource::collection($packages),
            'Packages retrieved successfully'
        );
    }

    public function show(Package $package): JsonResponse
    {
        return ApiResponseHelper::success(
            new PackageStudentResource($package),
            'Package retrieved successfully'
        );
    }

    public function getPackageCategories(): JsonResponse
    {
        // Load package categories along with related models
        $categories = PackageCategory::with([
            'package',
            'additionalPackageCategory'
            //  => function ($query) {
            //     $query->where('is_active', true); // Optional: Filter active categories
            // },
        ])->get();

        // Return the response with categories
        return ApiResponseHelper::success(
            $categories,
            'Package categories retrieved successfully'
        );
    }
}
