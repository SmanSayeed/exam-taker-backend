<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttachTypeRequest extends FormRequest
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
            'exam_type_id' => 'nullable|exists:exam_types,id|bail|required_if:section_id,null',
            'exam_sub_type_id' => 'nullable|exists:exam_sub_types,id|bail|required_if:exam_type_id,null',
            'group_id' => 'nullable|exists:groups,id',
            'level_id' => 'nullable|exists:levels,id|bail|required_if:group_id,null',
            'subject_id' => 'nullable|exists:subjects,id|bail|required_if:level_id,null',
            'lesson_id' => 'nullable|exists:lessons,id|bail|required_if:subject_id,null',
            'topic_id' => 'nullable|exists:topics,id|bail|required_if:lesson_id,null',
            'sub_topic_id' => 'nullable|exists:sub_topics,id|bail|required_if:topic_id,null',
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
