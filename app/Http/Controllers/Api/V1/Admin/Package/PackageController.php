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
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $packages = Package::paginate($perPage);

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
            $package = Package::create($request->validated());

            $package->packageCategory()->create($request->category);

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
            // Update the package details
            $package->update($request->validated());

            // If category data is provided in the request, update the related category
            if ($request->has('category')) {
                if ($package->packageCategory) {
                    // Update the existing category
                    $package->packageCategory()->update($request->category);
                } else {
                    // Create a new category if none exists
                    $package->packageCategory()->create($request->category);
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
            $package->delete();
            return ApiResponseHelper::success('Package deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to delete package', 500, $e->getMessage());
        }
    }

    public function changeStatus(ChangePackageStatusRequest $request, Package $package): JsonResponse
    {
        try {
            $package->update($request->all());
            return ApiResponseHelper::success(
                new PackageResource($package),
                'Package status changed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to change package status', 500, $e->getMessage());
        }
    }
}
