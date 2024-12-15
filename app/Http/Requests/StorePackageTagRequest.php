<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePackageTagRequest extends FormRequest
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
            'package_id' => 'required|exists:packages,id',
            'title' => 'required|string|max:255',
            'status' => 'boolean',
            'details' => 'nullable|string',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('validation errors occurred', 422, $errors->messages()));
    }
}
