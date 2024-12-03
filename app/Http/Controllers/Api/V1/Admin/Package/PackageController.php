<?php

namespace App\Http\Controllers\Api\V1\Admin\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PackageResource;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Http\Requests\Package\ChangePackageStatusRequest;
use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\PackageIndexRequest;
use App\Http\Resources\StudentResource\StudentResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function store(StorePackageRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Store the image if provided
            if ($request->hasFile('img')) {
                $data['img'] = $request->file('img')->store('packages', 'public');
            }

            // Create the package
            $package = Package::create($data);

            // Create or update the related package category (now as separate fields)
            if ($request->has('section_id') || $request->has('exam_type_id') || $request->has('exam_sub_type_id')) {
                $categoryData = [
                    'section_id' => $request->input('section_id'),
                    'exam_type_id' => $request->input('exam_type_id'),
                    'exam_sub_type_id' => $request->input('exam_sub_type_id')
                ];
                $package->packageCategory()->create($categoryData);
            }

            DB::commit();

            return ApiResponseHelper::success(
                new PackageResource($package),
                'Package created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to create package', 500, $e->getMessage());
        }
    }

    public function update(UpdatePackageRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Update the image if provided
            if ($request->hasFile('img')) {
                // Delete old image if exists
                if ($package->img) {
                    Storage::disk('public')->delete($package->img);
                }
                $data['img'] = $request->file('img')->store('packages', 'public');
            }
            Log::info($request->all());
            // Update package
            $package->update($data);

            // Update or create the related package category (now as separate fields)
            if ($request->has('section_id') || $request->has('exam_type_id') || $request->has('exam_sub_type_id')) {
                $categoryData = [
                    'section_id' => $request->input('section_id'),
                    'exam_type_id' => $request->input('exam_type_id'),
                    'exam_sub_type_id' => $request->input('exam_sub_type_id')
                ];

                if ($package->packageCategory) {
                    $package->packageCategory()->update($categoryData);
                } else {
                    $package->packageCategory()->create($categoryData);
                }
            }

            DB::commit();

            return ApiResponseHelper::success(
                new PackageResource($package),
                'Package updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to update package', 500, $e->getMessage());
        }
    }

    public function destroy(Package $package): JsonResponse
    {
        try {
            // Delete the package
            $package->delete();
            return ApiResponseHelper::success('Package deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to delete package', 500, $e->getMessage());
        }
    }

    public function changeStatus(ChangePackageStatusRequest $request, Package $package): JsonResponse
    {
        try {
            // Update package status
            $package->update($request->all());
            return ApiResponseHelper::success(
                new PackageResource($package),
                'Package status changed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to change package status', 500, $e->getMessage());
        }
    }

    public function getPackageSubscribers(Package $package, Request $request): JsonResponse
    {
        try {
            // Get per_page value from request or set default to 15
            $perPage = $request->get('per_page', 15);

            // Paginate the subscribers
            $subscribers = $package->subscribers()->paginate($perPage);

            return ApiResponseHelper::success(
                StudentResource::collection($subscribers),
                'Subscribers retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to get subscribers', 500, $e->getMessage());
        }
    }
}
