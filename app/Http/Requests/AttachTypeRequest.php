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

            // Section and exam type validation
            'section_id' => 'nullable|exists:sections,id',
            'exam_type_id' => 'nullable|exists:exam_types,id|required_with:exam_sub_type_id|required_if:section_id,null',
            'exam_sub_type_id' => 'nullable|exists:exam_sub_types,id|required_with:exam_type_id',

            // Group, level, and subject validation
            'group_id' => 'nullable|exists:groups,id|required_if:level_id,!null',
            'level_id' => 'nullable|exists:levels,id|required_with:subject_id|required_if:group_id,null',
            'subject_id' => 'nullable|exists:subjects,id|required_with:lesson_id|required_with_all:group_id,level_id|required_if:level_id,null',

            // Lesson, topic, and sub-topic validation
            'lesson_id' => 'nullable|exists:lessons,id|required_with:topic_id|required_if:subject_id,null',
            'topic_id' => 'nullable|exists:topics,id|required_with:sub_topic_id|required_if:lesson_id,null',
            'sub_topic_id' => 'nullable|exists:sub_topics,id|required_with:topic_id',
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
