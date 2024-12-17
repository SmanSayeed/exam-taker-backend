<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('student-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|in:bkash,nagad',
            'package_id' => 'required|exists:packages,id',
            'mobile_number' => 'required|string',
            'amount' => 'required|numeric',
            'coupon' => 'nullable|string',
            'transaction_id' => 'required|string|unique:student_payments,transaction_id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Get package ID from the route parameter directly
            $packageId = $this->route('package'); // This will return '1' or the actual ID as string.

            // Check if the user already has an active subscription for this package
            $existingSubscription = Subscription::where('student_id', Auth::id())
                ->where('package_id', $packageId)
                ->where('is_active', true)
                ->first();

            if ($existingSubscription) {
                $validator->errors()->add(
                    'subscription',
                    'You already have an active subscription for this package.'
                );
            }
        });
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error(' validation errors occurred', 422, $errors->messages()));
    }
}