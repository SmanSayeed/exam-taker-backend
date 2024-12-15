<?php

namespace App\Http\Controllers\Api\V1\Admin\PdfSubscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PdfSubscriptionIndexRequest;
use App\Http\Resources\PdfSubscriptionAdminResource;
use App\Models\PdfSubscription;

class PdfSubscriptionController extends Controller
{
    /**
     * Display a listing of the PDF subscriptions.
     */
    public function index(PdfSubscriptionIndexRequest $request)
    {
        $perPage = $request->get('per_page', 15);

        // Initialize query for PDF subscriptions
        $query = PdfSubscription::query();

        // Paginate the result based on per_page parameter
        $pdfSubscriptions = $query->paginate($perPage);

        return ApiResponseHelper::success(
            PdfSubscriptionAdminResource::collection($pdfSubscriptions),
            'PDF Subscriptions retrieved successfully'
        );
    }


    /**
     * Display a single PDF subscription.
     */
    public function show(PdfSubscription $pdf_subscription)
    {
        return ApiResponseHelper::success(
            new PdfSubscriptionAdminResource($pdf_subscription),
            'PDF Subscription retrieved successfully'
        );
    }

    /**
     * Activate a PDF subscription.
     */
    public function activateSubscription(PdfSubscription $pdf_subscription)
    {
        $pdf_subscription->update([
            'is_active' => true
        ]);

        return ApiResponseHelper::success(
            new PdfSubscriptionAdminResource($pdf_subscription),
            'PDF Subscription activated successfully'
        );
    }

    /**
     * Deactivate a PDF subscription.
     */
    public function deactivateSubscription(PdfSubscription $pdf_subscription)
    {
        $pdf_subscription->update([
            'is_active' => false
        ]);

        return ApiResponseHelper::success(
            new PdfSubscriptionAdminResource($pdf_subscription),
            'PDF Subscription deactivated successfully'
        );
    }
}
