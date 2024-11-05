<?php

namespace App\Http\Requests\Student;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class StudentProfileUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:students,phone,' . $this->user()->id,
            'profile_image' => 'nullable|image|max:2048',
            'country' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|max:5',
            'address' => 'nullable|string|max:255',
        ];
    }

    protected function prepareForValidation()
    {
        // Log the request body
        Log::info('Student registration request body:', $this->all());
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages()));
    }
}
