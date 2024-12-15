<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PackageIndexRequest extends FormRequest
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
            'section_id' => 'sometimes|integer|exists:sections,id',
            'exam_type_id' => 'sometimes|integer|exists:exam_types,id',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error(' validation errors occurred', 422, $errors->messages()));
    }
}
