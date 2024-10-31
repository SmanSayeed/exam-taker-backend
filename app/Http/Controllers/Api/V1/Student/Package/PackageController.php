<?php

namespace App\Http\Controllers\Api\V1\Student\Package;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PackageIndexRequest;
use App\Http\Resources\PackageResource;
use App\Models\Package;
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
        $packages = $query->paginate($perPage);

        return ApiResponseHelper::success(
            PackageResource::collection($packages),
            'Packages retrieved successfully'
        );
    }

    public function show(Package $package): JsonResponse
    {
        return ApiResponseHelper::success(
            new PackageResource($package),
            'Package retrieved successfully'
        );
    }
}
