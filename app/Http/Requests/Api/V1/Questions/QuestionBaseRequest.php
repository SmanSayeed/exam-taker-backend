<?php

namespace App\Http\Requests\Api\V1\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionBaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Here you can add authorization logic based on the user's role or permissions
        return Auth::guard('admin-api')->check();
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'required|boolean',
        ];
        switch ($this->route('resourceType')) {
            case 'exam-types':
                $rules['section_id'] = 'required|exists:sections,id';
                $rules['year'] = 'nullable|string';
                break;
            case 'exam-sub-types':
                $rules['exam_type_id'] = 'required|exists:exam_types,id';
                break;
            case 'subjects':
                $rules['level_id'] = 'required|exists:levels,id';
                $rules['group_id'] = 'required|exists:groups,id';
                $rules['part'] = 'required|in:1,2';
                break;
            case 'lessons':
                $rules['subject_id'] = 'required|exists:subjects,id';
                break;
            case 'topics':
                $rules['lesson_id'] = 'required|exists:lessons,id';
                break;
            case 'sub-topics':
                $rules['topic_id'] = 'required|exists:topics,id';
                break;
            case 'level':
                    $rules['group_id'] = 'required|exists:groups,id';
                    break;
        }

        return $rules;
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
