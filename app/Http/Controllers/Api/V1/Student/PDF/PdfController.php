<?php

namespace App\Http\Controllers\Api\V1\Student\PDF;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentPdfResource;
use App\Models\Pdf;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $pdfs = Pdf::paginate($perPage);

        return ApiResponseHelper::success(StudentPdfResource::collection($pdfs), 'PDF titles retrieved successfully');
    }

    public function show(Pdf $pdf): JsonResponse
    {
        $student = Auth::guard('student-api')->user();

        $isSubscribed = Subscription::where('student_id', $student->id)
            ->where('is_active', true)
            ->where('expires_at', '>=', now())
            ->exists();

        if (!$isSubscribed) {
            return ApiResponseHelper::error('You must be subscribed to view this PDF', 403);
        }

        // Return the full PDF details
        return ApiResponseHelper::success(new StudentPdfResource($pdf), 'PDF retrieved successfully');
    }
}
