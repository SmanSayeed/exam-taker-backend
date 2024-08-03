<?php

namespace App\Http\Requests\Api\V1\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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

        switch ($this->route('type')) {
            case 'exam-types':
                $rules['section_id'] = 'required|exists:sections,id';
                break;
            case 'exam-sub-types':
                $rules['exam_type_id'] = 'required|exists:exam_types,id';
                break;
            case 'years':
                $rules['section_id'] = 'required|exists:sections,id';
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
        }

        return $rules;
    }
}
