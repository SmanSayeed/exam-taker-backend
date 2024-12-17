<?php

namespace App\Http\Controllers\Api\V1\Admin\PDF;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePdfRequest;
use App\Http\Requests\UpdatePdfRequest;
use App\Http\Resources\AdminPdfResource;
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
            AdminPdfResource::collection($pdfs),
            'PDFs retrieved successfully'
        );
    }

    public function store(StorePdfRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();  // Get the validated data

            // Store the file if provided and add the file path to the data array
            if ($request->hasFile('file')) {
                $data['file_path'] = $request->file('file')->store('pdf', 'public');
            }

            if ($request->hasFile('img')) {
                $data['img'] = $request->file('img')->store('pdf', 'public');
            }

            // Create the PDF record with the validated data (including the file path)
            $pdf = Pdf::create($data);  // Save the data including the file_path

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
