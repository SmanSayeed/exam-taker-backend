<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
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
            'package_id' => [
                'required',
                'exists:packages,id', // Ensure package exists in the 'packages' table
                Rule::unique('subscriptions')->where(function ($query) {
                    return $query->where('student_id', $this->student_id)
                        ->where('package_id', $this->package_id);
                }),
            ],
            'is_active' => 'required|boolean', // Ensure is_active is a boolean value
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'package_id.unique' => 'This student has already subscribed to this package.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('validation errors occurred', 422, $errors->messages()));
    }
}
