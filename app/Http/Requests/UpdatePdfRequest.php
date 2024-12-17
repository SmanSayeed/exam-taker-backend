<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePdfRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'file' => 'sometimes|file|mimes:pdf|max:10240',
            'file_link' => 'sometimes|url',
            'is_active' => 'sometimes|boolean',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mime_type' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Update validation errors occurred', 422, $errors->messages()));
    }
}
