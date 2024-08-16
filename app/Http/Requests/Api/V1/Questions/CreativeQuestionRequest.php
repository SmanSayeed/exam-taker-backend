<?php

namespace App\Http\Requests\Api\V1\Questions;
use App\Rules\UniqueCreativeQuestionType;
use App\Rules\ValidateQuestionType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class CreativeQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check();
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'exists:questions,id', new ValidateQuestionType('creative')],
            'creative_question_text' => 'required|string',
            'creative_question_type' => [
                'required',
                'string',
                'in:a,b,c,d',
                // new UniqueCreativeQuestionType($this->question_id, $this->creative_question_type)
            ],
            'description' => 'nullable|string',
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
