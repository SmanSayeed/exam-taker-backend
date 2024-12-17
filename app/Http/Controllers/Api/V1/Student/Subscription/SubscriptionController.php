<?php

namespace App\Http\Controllers\Api\V1\Student\Subscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\SubscriptionStudentResource;
use App\Models\Package;
use App\Models\StudentPayment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);

        // Ensure only subscriptions for the authenticated user are retrieved
        $subscriptions = Subscription::where('student_id', Auth::id())->paginate($perPage);

        return ApiResponseHelper::success(SubscriptionStudentResource::collection($subscriptions), 'Subscriptions retrieved successfully');
    }

    public function show(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            return ApiResponseHelper::error('Unauthorized access to this subscription', 403);
        }

        return ApiResponseHelper::success(
            new SubscriptionStudentResource($subscription),
            'Subscription retrieved successfully'
        );
    }

    //get all subscribed packages
    // public function getSubscribedPackages()
    // {
    //     // Retrieve all packages the authenticated student has subscribed to
    //     $packages = Subscription::where('student_id', Auth::id())
    //         ->with('package') // Eager load the package relationship
    //         ->get()
    //         ->pluck('package') // Extract only the package from each subscription
    //         ->unique(); // Ensure no duplicate packages are returned

    //     // Return as a collection of PackageResource
    //     return ApiResponseHelper::success(PackageResource::collection($packages), 'Subscribed packages retrieved successfully');
    // }
}
