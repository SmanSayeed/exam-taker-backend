<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AttachExaminationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check(); // Adjust this guard as necessary
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'examination_id' => [
                'required',
                'exists:examinations,id',
                Rule::exists('examinations', 'id')->where(function ($query) {
                    $query->where('is_paid', true); // Example condition, update as needed
                }),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'examination_ids.required' => 'You must provide at least one examination ID.',
            'examination_ids.array' => 'The examination IDs must be an array.',
            'examination_ids.*.exists' => 'One or more of the selected examinations are not valid or are not paid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages()));
    }
}
