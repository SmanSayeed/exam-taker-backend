<?php

namespace App\Http\Controllers\Api\V1\Student\PdfSubscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPdfResource;
use App\Http\Resources\PdfSubscriptionStudentResource;
use App\Models\PdfSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

class PdfSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);

        // Ensure only subscriptions for the authenticated user are retrieved
        $subscriptions = PdfSubscription::where('student_id', Auth::id())->paginate($perPage);

        return ApiResponseHelper::success(PdfSubscriptionStudentResource::collection($subscriptions), 'Subscriptions retrieved successfully');
    }

    public function show(PdfSubscription $subscription)
    {
        Log::info(Auth::id());
        if ($subscription->student_id !== Auth::id()) {
            return ApiResponseHelper::error('Unauthorized access to this subscription', 403);
        }

        return ApiResponseHelper::success(
            new PdfSubscriptionStudentResource($subscription),
            'PdfSubscription retrieved successfully'
        );
    }

    //get all subscribed pdfs
    public function getSubscribedPdfs()
    {
        // Retrieve all pdfs the authenticated student has subscribed to
        $pdfs = PdfSubscription::where('student_id', Auth::id())
            ->with('pdf') // Eager load the pdf relationship
            ->get()
            ->pluck('pdf') // Extract only the pdf from each subscription
            ->unique(); // Ensure no duplicate pdfs are returned

        // Return as a collection of PdfResource
        return ApiResponseHelper::success(AdminPdfResource::collection($pdfs), 'Subscribed pdfs retrieved successfully');
    }
}
