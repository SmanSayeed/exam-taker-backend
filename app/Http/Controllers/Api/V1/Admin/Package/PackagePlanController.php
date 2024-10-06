<?php

namespace App\Http\Controllers\Api\V1\Admin\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackagePlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\PackagePlanResource;
use App\Http\Requests\PackagePlans\StorePackagePlanRequest;
use App\Http\Requests\PackagePlans\UpdatePackagePlanRequest;
use App\Http\Requests\PackagePlans\ChangePackagePlanStatusRequest;
use Illuminate\Support\Facades\Log;

class PackagePlanController extends Controller
{
    public function index(Package $package): JsonResponse
    {
        try {
            $plans = $package->plans; // Get all plans related to the package

            return ApiResponseHelper::success(
                PackagePlanResource::collection($plans), // Transform each plan using the resource
                'Package plans retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve package plans', 500, $e->getMessage());
        }
    }

    public function show(Package $package, PackagePlan $plan): JsonResponse
    {
        return ApiResponseHelper::success(
            new PackagePlanResource($plan),
            'Package plan retrieved successfully'
        );
    }

    public function store(StorePackagePlanRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Create the plan associated with the package
            $plan = $package->plans()->create($request->validated()); // Use validated data

            // Eager load the 'package' relationship
            $plan->load('package');

            DB::commit();

            // Return the response with the newly created plan and package details
            return ApiResponseHelper::success(
                new PackagePlanResource($plan), // Transform the created plan using the resource
                'Package plan created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to create package plan', 500, $e->getMessage());
        }
    }

    public function update(UpdatePackagePlanRequest $request, Package $package, PackagePlan $plan): JsonResponse
    {
        DB::beginTransaction();
        try {
            $plan->update($request->all());
            DB::commit();
            return ApiResponseHelper::success(
                new PackagePlanResource($plan),
                'Package plan updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to update package plan', 500, $e->getMessage());
        }
    }

    public function destroy(Package $package, PackagePlan $plan): JsonResponse
    {
        try {
            $plan->delete();
            return ApiResponseHelper::success('Package plan deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to delete package plan', 500, $e->getMessage());
        }
    }

    public function changeStatus(ChangePackagePlanStatusRequest $request, Package $package, PackagePlan $plan): JsonResponse
    {
        try {
            $plan->update($request->all());
            return ApiResponseHelper::success(
                new PackagePlanResource($plan),
                'Package plan status changed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to change package plan status', 500, $e->getMessage());
        }
    }
}
