<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Questions\SectionRequest;
use App\Services\SectionService;
use App\Models\Section;
use App\Helpers\ApiResponseHelper;
use Illuminate\Http\JsonResponse;
use Exception;

class SectionController extends Controller
{
    protected SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function store(SectionRequest $request): JsonResponse
    {
        try {
            $section = $this->sectionService->createSection($request->validated());
            return ApiResponseHelper::success($section, 'Section created successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create section: ' . $e->getMessage());
        }
    }

    public function update(SectionRequest $request, Section $section): JsonResponse
    {
        try {
            $section = $this->sectionService->updateSection($section, $request->validated());
            return ApiResponseHelper::success($section, 'Section updated successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update section: ' . $e->getMessage());
        }
    }

    public function destroy(Section $section): JsonResponse
    {
        try {
            $this->sectionService->deleteSection($section);
            return ApiResponseHelper::success([], 'Section deleted successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete section: ' . $e->getMessage());
        }
    }

    public function changeStatus(Section $section, $status): JsonResponse
    {
        try {
            $section = $this->sectionService->changeStatus($section, (bool)$status);
            return ApiResponseHelper::success($section, 'Section status changed successfully');
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to change section status: ' . $e->getMessage());
        }
    }
}
