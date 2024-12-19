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
            'exam_type_id' =>'nullable',
            'exam_sub_type_id' => 'nullable'
            ]
            ;
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

    public function messages()
    {
        return [
            'section_id.exists' => 'The selected section is invalid.',
            'exam_type_id.exists' => 'The selected exam type is invalid or does not belong to the selected section.',
            'exam_sub_type_id.exists' => 'The selected exam sub type is invalid or does not belong to the selected exam type.',
            'exam_type_id.required' => 'The exam type field is required when exam sub type is filled.',
            'exam_sub_type_id.required' => 'The exam sub type field is required when exam type is filled.',
        ];
    }

    public function attributes()
    {
        return [
            'section_id' => 'section',
            'exam_type_id' => 'exam type',
            'exam_sub_type_id' => 'exam sub type',
        ];
    }
}
