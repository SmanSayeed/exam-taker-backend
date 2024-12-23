<?php

namespace App\Http\Requests\Api\V1\Questions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class QuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'is_paid' => 'required|boolean',
            'is_featured' => 'required|boolean',
            'type' => 'required|in:mcq,creative,normal',
            'status'=>'required|boolean',
            'mark' => 'required|integer',
            'created_by'=>'nullable|string',
            'edited_by'=>'nullable|string',
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
