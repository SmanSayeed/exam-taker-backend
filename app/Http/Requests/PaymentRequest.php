<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'payment_method' => 'required|string|in:bkash,nagad,rocket', // Validate payment method
            'mobile_number' => 'nullable|string|size:11', // Validate mobile number (adjust as needed)
            'transaction_id' => 'required|string|unique:student_payments,transaction_id', // Ensure unique transaction ID
            'amount' => 'required|numeric|min:1', // Amount should be a positive number
            'coupon' => 'nullable|string', // Optional coupon code
            'verified' => 'nullable|boolean', // Boolean value for verification status
            'verified_at' => 'nullable|date|after_or_equal:created_at', // Verified timestamp (if applicable)
            'resource_type' => 'required|in:pdf,package', // Resource type validation
            'resource_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $resourceType = $this->input('resource_type');
                    if ($resourceType === 'pdf') {
                        // Check if resource_id exists in the pdf table
                        if (!\App\Models\Pdf::find($value)) {
                            $fail('The selected resource_id is invalid for pdf.');
                        }
                    } elseif ($resourceType === 'package') {
                        // Check if resource_id exists in the package table
                        if (!\App\Models\Package::find($value)) {
                            $fail('The selected resource_id is invalid for package.');
                        }
                    }
                }
            ]
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages()));
    }
}
