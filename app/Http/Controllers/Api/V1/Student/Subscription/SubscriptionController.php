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

    public function pay(PaymentRequest $request, Package $package)
    {
        DB::beginTransaction();
        try {
            // Create a payment record, storing the package_id and amount explicitly
            $payment = StudentPayment::create([
                'student_id'       => Auth::id(),
                'package_id'       => $package->id, // Store package ID
                'payment_method'   => $request->payment_method,  // e.g., bkash, nagad
                'mobile_number'    => $request->mobile_number,
                'transaction_id'   => $request->transaction_id,
                'amount'           => $request->amount,          // Payment amount
                'coupon'           => $request->coupon,
            ]);

            DB::commit();

            return ApiResponseHelper::success([
                'payment' => [
                    'package_id'      => $payment->package_id,
                    'payment_method'  => $payment->payment_method,
                    'mobile_number'   => $payment->mobile_number,
                    'transaction_id'  => $payment->transaction_id,
                    'amount'          => $payment->amount,
                    'coupon'          => $payment->coupon,
                ],
            ], 'Payment processed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Payment failed: ' . $e->getMessage(), 500);
        }
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
