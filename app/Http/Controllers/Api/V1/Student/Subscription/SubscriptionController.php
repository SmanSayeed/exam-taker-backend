<?php

namespace App\Http\Controllers\Api\V1\Student\Subscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Resources\PackageResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Package;
use App\Models\StudentPayment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);

        // Ensure only subscriptions for the authenticated user are retrieved
        $subscriptions = Subscription::where('student_id', Auth::id())->paginate($perPage);

        return ApiResponseHelper::success(SubscriptionResource::collection($subscriptions), 'Subscriptions retrieved successfully');
    }

    public function show(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            return ApiResponseHelper::error('Unauthorized access to this subscription', 403);
        }

        return ApiResponseHelper::success(
            new SubscriptionResource($subscription),
            'Subscription retrieved successfully'
        );
    }



    public function subscribe(SubscriptionRequest $request, Package $package)
    {
        DB::beginTransaction();

        try {
            // Check if the student already has an active subscription for this package
            $existingSubscription = Subscription::where('student_id', Auth::id())
                ->where('package_id', $package->id)
                ->first();

            if ($existingSubscription) {
                return ApiResponseHelper::error('You are already subscribed to this package.', 400);
            }

            // Create a new subscription
            $subscription = Subscription::create([
                'student_id' => Auth::id(),
                'package_id' => $package->id,
                'subscribed_at' => now(),
            ]);

            // Create a new transaction record
            $transaction = StudentPayment::create([
                'subscription_id' => $subscription->id,
                'payment_method'  => $request->payment_method, // e.g., 'bkash', 'nagad'
                'mobile_number'   => $request->mobile_number,  // Mobile banking number
                'transaction_id'  => $request->transaction_id, // Transaction ID
                'amount'          => $request->amount,
                'package_id'      => $package->id,
            ]);

            DB::commit();

            $response = [
                'subscription' => new SubscriptionResource($subscription),
                'transaction' => [
                    'payment_method'  => $transaction->payment_method,
                    'mobile_number'   => $transaction->mobile_number,
                    'transaction_id'  => $transaction->transaction_id,
                    'amount'          => $transaction->amount,
                ],
            ];

            return ApiResponseHelper::success($response, 'Subscription created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Something went wrong: ' . $e->getMessage(), 500);
        }
    }


    //get all subscribed packages
    public function getSubscribedPackages()
    {
        // Retrieve all packages the authenticated student has subscribed to
        $packages = Subscription::where('student_id', Auth::id())
            ->with('package') // Eager load the package relationship
            ->get()
            ->pluck('package') // Extract only the package from each subscription
            ->unique(); // Ensure no duplicate packages are returned

        // Return as a collection of PackageResource
        return ApiResponseHelper::success(PackageResource::collection($packages), 'Subscribed packages retrieved successfully');
    }
}
