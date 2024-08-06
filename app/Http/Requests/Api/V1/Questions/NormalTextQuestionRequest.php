<?php

namespace App\Http\Requests\Api\V1\Questions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class NormalTextQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check();
    }

    public function rules(): array
    {
        return [
            'normal_question_description' => 'required|string',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(), // This will include field-specific errors
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }
}
