<?php

namespace App\Http\Controllers\Api\V1\Admin\Subscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionIndexRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    public function index(SubscriptionIndexRequest $request)
    {
        $perPage = $request->get('per_page', 15);

        // Initialize query for subscriptions
        $query = Subscription::query();

        // Apply filter for is_active if it exists in the request
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        // Paginate the result based on per_page parameter
        $subscriptions = $query->paginate($perPage);

        return ApiResponseHelper::success(SubscriptionResource::collection($subscriptions), 'Subscriptions retrieved successfully');
    }


    public function show(Subscription $subscription)
    {
        return ApiResponseHelper::success(
            new SubscriptionResource($subscription),
            'Subscription retrieved successfully'
        );
    }

    public function activateSubscription(Subscription $subscription)
    {

        $subscription->update([
            'is_active' => true
        ]);

        return ApiResponseHelper::success(
            new SubscriptionResource($subscription),
            'Subscription activated successfully'
        );
    }
    public function deactivateSubscription(Subscription $subscription)
    {
        $subscription->update([
            'is_active' => false
        ]);

        return ApiResponseHelper::success(
            new SubscriptionResource($subscription),
            'Subscription deactivated successfully'
        );
    }
}
