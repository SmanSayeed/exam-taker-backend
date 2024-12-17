<?php

namespace App\Http\Controllers\Api\V1\Admin\PdfSubscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePdfSubscriptionRequest;
use App\Http\Requests\PdfSubscriptionIndexRequest;
use App\Http\Requests\UpdatePdfSubscriptionRequest;
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
    public function store(CreatePdfSubscriptionRequest $request)
    {
        // Validate the request through ActivateSubscriptionRequest

        // Get validated data
        $validatedData = $request->validated();

        // Create a new subscription
        $pdf_subscription = PdfSubscription::create($validatedData);

        return ApiResponseHelper::success(
            new PdfSubscriptionAdminResource($pdf_subscription),
            'PDF Subscription created successfully'
        );
    }

    /**
     * Deactivate a PDF subscription.
     */
    public function update(UpdatePdfSubscriptionRequest $request, PdfSubscription $pdf_subscription)
    {
        // Validate the request
        $validatedData = $request->validated();

        // Update the subscription with new data
        $pdf_subscription->update($validatedData);

        return ApiResponseHelper::success(
            new PdfSubscriptionAdminResource($pdf_subscription),
            'Subscription updated successfully'
        );
    }
    public function destroy(PdfSubscription $pdf_subscription)
    {
        if ($pdf_subscription->active) {
            return ApiResponseHelper::error('Active subscription cannot be deleted', 400);
        }
        $pdf_subscription->delete();
        return ApiResponseHelper::success(null, 'PDF Subscription deleted successfully');
    }
}
