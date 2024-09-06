<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartExamRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this if you have authorization logic
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:mcq,creative,normal',
            'section_categories' => 'nullable|array',
            'section_categories.*' => 'integer',
            'exam_type_categories' => 'nullable|array',
            'exam_type_categories.*' => 'integer',
            'exam_sub_type_categories' => 'nullable|array',
            'exam_sub_type_categories.*' => 'integer',
            'group_categories' => 'nullable|array',
            'group_categories.*' => 'integer',
            'level_categories' => 'nullable|array',
            'level_categories.*' => 'integer',
            'lesson_categories' => 'nullable|array',
            'lesson_categories.*' => 'integer',
            'topic_categories' => 'nullable|array',
            'topic_categories.*' => 'integer',
            'sub_topic_categories' => 'nullable|array',
            'sub_topic_categories.*' => 'integer',
            'time_limit' => 'required|numeric',
            'created_by' => 'required|integer',
            'created_by_role' => 'required|in:admin,student',
            'questions_limit' => 'nullable|integer|min:1',
            'student_id' => 'required|integer',
        ];
    }
}
