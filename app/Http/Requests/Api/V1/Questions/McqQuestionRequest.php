<?php
namespace App\Http\Requests\Api\V1\Questions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\ValidateQuestionType;

class McqQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check();
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'exists:questions,id', new ValidateQuestionType('mcq')],
            '' => 'required|string',
            'is_correct' => 'required|boolean',
            'description' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = [
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 422));
    }
}
