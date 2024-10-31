<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ModelTestIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => 'sometimes|integer|exists:groups,id',
            'level_id' => 'sometimes|integer|exists:levels,id',
            'subject_id' => 'sometimes|integer|exists:subjects,id',
            'lesson_id' => 'sometimes|integer|exists:lessons,id',
            'topic_id' => 'sometimes|integer|exists:topics,id',
            'sub_topic_id' => 'sometimes|integer|exists:sub_topics,id',
        ];
    }
}
