<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdditionalPackageCategoryRequest;
use App\Http\Requests\UpdateAdditionalPackageCategoryRequest;
use App\Http\Resources\AdditionalPackageCategoryResource;
use App\Models\AdditionalPackageCategory;
use Illuminate\Http\Request;

class AdditionalPackageCategoryController extends Controller
{
    /**
     * Display a listing of additional package categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);

        // Initialize query for additional package categories
        $query = AdditionalPackageCategory::query();

        // Paginate the result based on per_page parameter
        $categories = $query->paginate($perPage);

        return ApiResponseHelper::success(
            AdditionalPackageCategoryResource::collection($categories),
            'Additional package categories retrieved successfully'
        );
    }

    /**
     * Display the specified additional package category.
     *
     * @param  AdditionalPackageCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(AdditionalPackageCategory $additional_package_category)
    {

        return ApiResponseHelper::success(
            new AdditionalPackageCategoryResource($additional_package_category),
            'Additional package category retrieved successfully'
        );
    }

    /**
     * Store a new additional package category.
     *
     * @param  StoreAdditionalPackageCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdditionalPackageCategoryRequest $request)
    {
        // Validate and create the category
        $category = AdditionalPackageCategory::create($request->validated());

        return ApiResponseHelper::success(
            new AdditionalPackageCategoryResource($category),
            'Additional package category created successfully'
        );
    }

    /**
     * Update the specified additional package category.
     *
     * @param  UpdateAdditionalPackageCategoryRequest  $request
     * @param  AdditionalPackageCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdditionalPackageCategoryRequest $request, AdditionalPackageCategory $additional_package_category)
    {
        // Validate and update the category
        $additional_package_category->update($request->validated());

        return ApiResponseHelper::success(
            new AdditionalPackageCategoryResource($additional_package_category),
            'Additional package category updated successfully'
        );
    }

    public function destroy(AdditionalPackageCategory $additional_package_category)
    {
        // Soft delete the category
        $additional_package_category->delete();

        return ApiResponseHelper::success(
            null,
            'Additional package category deleted successfully'
        );
    }
}
