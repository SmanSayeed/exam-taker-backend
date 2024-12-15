<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use App\Models\PdfSubscriptionPayment;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PdfPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure user is authenticated via 'student-api' guard
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
            'mobile_number' => 'required|string|regex:/^\+?\d{10,15}$/', // Ensure valid phone format
            'amount' => 'required|numeric|min:1', // Amount must be positive
            'coupon' => 'nullable|string',
            'transaction_id' => 'required|string|unique:pdf_subscription_payments,transaction_id',
        ];
    }
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Return a JSON error response using the ApiResponseHelper
        throw new HttpResponseException(
            ApiResponseHelper::error('Validation errors occurred.', 422, $errors->messages())
        );
    }
}
