<?php

namespace App\Http\Controllers\Api\V1\Admin\PdfSubscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\PdfSubscriptionPaymentResource;
use App\Models\PdfSubscriptionPayment;

class PdfSubscriptionPaymentController extends Controller
{
    /**
     * Display a listing of the PDF subscription payments.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15); // Default pagination value is 15
        $pdfPayments = PdfSubscriptionPayment::paginate($perPage);
        return ApiResponseHelper::success(
            PdfSubscriptionPaymentResource::collection($pdfPayments),
            'PDF Subscription Payments retrieved successfully'
        );
    }

    /**
     * Display a single PDF subscription payment.
     */
    public function show(PdfSubscriptionPayment $pdf_subscription_payment): JsonResponse
    {
        return ApiResponseHelper::success(
            new PdfSubscriptionPaymentResource($pdf_subscription_payment),
            'PDF Subscription Payment retrieved successfully'
        );
    }

    /**
     * Remove the specified PDF subscription payment.
     */
    public function destroy(PdfSubscriptionPayment $pdf_subscription_payment): JsonResponse
    {
        // Check if the payment is verified
        if ($pdf_subscription_payment->verified) {
            return ApiResponseHelper::error('Verified payments cannot be deleted', 400);
        }

        // Proceed with deletion if not verified
        $pdf_subscription_payment->delete();
        return ApiResponseHelper::success(null, 'PDF Subscription Payment deleted successfully');
    }
}
