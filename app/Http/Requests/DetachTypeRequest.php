<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DetachTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin-api')->check();
    }

    public function rules()
    {
        return [
            'question_id' => 'required|exists:questions,id',
            'section_id' => 'nullable|exists:sections,id',
            'exam_type_id' => 'nullable|exists:exam_types,id',
            'exam_sub_type_id' => 'nullable|exists:exam_sub_types,id',
            'group_id' => 'nullable|exists:groups,id',
            'level_id' => 'nullable|exists:levels,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'topic_id' => 'nullable|exists:topics,id',
            'sub_topic_id' => 'nullable|exists:sub_topics,id',
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
