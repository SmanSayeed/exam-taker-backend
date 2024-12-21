<?php

namespace App\Http\Controllers\Api\V1\Admin\Subscription;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionIndexRequest;
use App\Http\Requests\ActivateSubscriptionRequest;  // Assuming a request class is used for create and update
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\SubscriptionUpdateRequest;   // A request class for validating subscription updates
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionAdminResource;
use App\Models\Subscription;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions.
     *
     * @param  SubscriptionIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
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

        return ApiResponseHelper::success(SubscriptionAdminResource::collection($subscriptions), 'Subscriptions retrieved successfully');
    }

    /**
     * Display the specified subscription.
     *
     * @param  Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        return ApiResponseHelper::success(
            new SubscriptionAdminResource($subscription),
            'Subscription retrieved successfully'
        );
    }

    public function store(CreateSubscriptionRequest $request)
    {
        // Get validated data from the request
        $validatedData = $request->validated();

        // Find the package based on the package_id
        $package = Package::findOrFail($validatedData['package_id']);

        // Assuming the package has a 'duration' field in days
        $expiresAt = now()->addDays($package->duration_days); // Add duration (in days) to the current date

        // Set the expires_at date in the validated data
        $validatedData['expires_at'] = $expiresAt;
        //log expire date
        Log::info($expiresAt);
        Log::info($package->duration_days);
        // Create the new subscription
        $subscription = Subscription::create($validatedData);

        return ApiResponseHelper::success(
            new SubscriptionAdminResource($subscription),
            'Subscription created successfully'
        );
    }


    /**
     * Update an existing subscription.
     *
     * @param  SubscriptionUpdateRequest  $request
     * @param  Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        // Validate the request
        $validatedData = $request->validated();

        // Update the subscription with new data
        $subscription->update($validatedData);

        return ApiResponseHelper::success(
            new SubscriptionAdminResource($subscription),
            'Subscription updated successfully'
        );
    }

    /**
     * Delete a subscription.
     *
     * @param  Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        // Delete the subscription
        $subscription->delete();

        return ApiResponseHelper::success(
            null,
            'Subscription deleted successfully'
        );
    }
}
