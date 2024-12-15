<?php

namespace App\Http\Controllers\Api\V1\Admin\PDF;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePdfRequest;
use App\Http\Requests\UpdatePdfRequest;
use App\Http\Resources\AdminPdfResource;
use App\Http\Resources\StudentPdfResource;
use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Log::info($request);
        $perPage = $request->get('per_page', 15);
        $pdfs = Pdf::paginate($perPage);
        return ApiResponseHelper::success(
            StudentPdfResource::collection($pdfs),
            'PDFs retrieved successfully'
        );
    }

    public function store(StorePdfRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Validate and store the PDF document
            $pdf = Pdf::create($request->validated());
            DB::commit();

            return ApiResponseHelper::success(
                new AdminPdfResource($pdf),
                'PDF created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to create PDF', 500, $e->getMessage());
        }
    }

    public function update(UpdatePdfRequest $request, Pdf $pdf): JsonResponse
    {
        DB::beginTransaction();
        try {
            $pdf->update($request->validated());
            DB::commit();

            return ApiResponseHelper::success(
                new AdminPdfResource($pdf),
                'PDF updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to update PDF', 500, $e->getMessage());
        }
    }

    public function destroy(Pdf $pdf): JsonResponse
    {
        DB::beginTransaction();
        try {
            $pdf->delete();
            DB::commit();

            return ApiResponseHelper::success('PDF deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to delete PDF', 500, $e->getMessage());
        }
    }

    public function show(Pdf $pdf): JsonResponse
    {
        return ApiResponseHelper::success(
            new AdminPdfResource($pdf),
            'PDF retrieved successfully'
        );
    }
}
