<?php

namespace App\Http\Controllers\Api\V1\Student\StudentPdfPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\PdfSubscriptionPaymentResource;
use App\Models\PdfSubscriptionPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PdfSubscriptionPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $pdfSubscriptionPayments = PdfSubscriptionPayment::where('student_id', Auth::id())->paginate($perPage);
        return ApiResponseHelper::success(
            PdfSubscriptionPaymentResource::collection($pdfSubscriptionPayments),
            'Subscription payments retrieved successfully'
        );
    }

    public function show(PdfSubscriptionPayment $pdf_subscription_payment): JsonResponse
    {
        Log::info($pdf_subscription_payment);
        Log::info(Auth::id(), ['student_id' => $pdf_subscription_payment->student_id]);
        if ($pdf_subscription_payment->student_id !== Auth::id()) {
            return ApiResponseHelper::error('Unauthorized access to this transaction', 403);
        }

        return ApiResponseHelper::success(
            new PdfSubscriptionPaymentResource($pdf_subscription_payment),
            'Subscription payment retrieved successfully'
        );
    }
}
