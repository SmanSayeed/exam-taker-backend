<?php

namespace App\Http\Requests\Package;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'price' => 'nullable|numeric',
            'duration_days' => 'nullable|numeric',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:percentage,amount',
            'section_id' => 'nullable|exists:sections,id', // section_id is now optional for update
            'exam_type_id' => 'nullable',
            'exam_sub_type_id' => 'nullable',
            'tag_ids' => 'nullable|array', // Ensure it is an array
            'tag_ids.*' => 'integer|exists:tags,id', // Each item in the array must be a valid tag ID
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        Log::error('Validation Errors:', $errors->messages());

        throw new HttpResponseException(
            ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages())
        );
    }

    /**
     * Log request data before validation.
     */
    protected function prepareForValidation()
    {
        Log::info('Incoming request data:', $this->all());
    }
}
