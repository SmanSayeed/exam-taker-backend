<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishMTExamRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization logic, if any
    }

    public function rules()
    {
        return [
            'examination_id' => 'required|exists:examinations,id',
            'student_id' => 'required|exists:students,id',
            'mcq_answers' => 'nullable|array',
            'mcq_answers.*.question_id' => 'required_with:mcq_answers|integer|exists:questions,id',
            'mcq_answers.*.mcq_question_id' => 'required_with:mcq_answers|integer',
            'mcq_answers.*.submitted_mcq_option' => 'string|nullable',
            'creative_answers' => 'nullable|array',
            'creative_answers.*.question_id' => 'required_with:creative_answers|integer|exists:questions,id',
            'creative_answers.*.creative_question_id' => 'required_with:creative_answers|integer',
            'creative_answers.*.creative_question_option' => 'required_with:creative_answers|string',
            'creative_answers.*.creative_question_text' => 'string|nullable',
            'normal_answers' => 'nullable|array',
            'normal_answers.*.question_id' => 'required_with:normal_answers|integer|exists:questions,id',
            'normal_answers.*.normal_answer_text' => 'string|nullable',
        ];
    }
}
