<?php

namespace App\Http\Requests\Package;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Logging
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Log the request data
        Log::info('StorePackageRequest Authorization Check', [
            'user_id' => Auth::id(),
            'request_data' => $this->all(),
        ]);

        return Auth::guard('admin-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Log the validation rules being applied
        Log::info('StorePackageRequest Validation Rules Applied', [
            'request_data' => $this->all(),
        ]);

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'price' => 'required|numeric',
            'duration_days' => 'required|numeric',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:percentage,amount',
            'additional_package_category_id' => 'nullable|exists:additional_package_categories,id',
            'section_id' => 'nullable|exists:sections,id', // section_id is now required
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

        // Log validation errors
        Log::error('StorePackageRequest Validation Failed', [
            'errors' => $errors->messages(),
            'request_data' => $this->all(),
        ]);

        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages()));
    }

    public function messages()
    {
        return [
            'section_id.required' => 'The section field is required.',
        ];
    }
}
