<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePdfSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id', // Ensure student exists in the 'students' table
            'pdf_id' => 'required|exists:pdfs,id', // Ensure package exists in the 'packages' table
            'expires_at' => 'required|date|after_or_equal:today', // Ensure expiration date is valid
            'is_active' => 'required|boolean', // Ensure is_active is a boolean value
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('validation errors occurred', 422, $errors->messages()));
    }
}
