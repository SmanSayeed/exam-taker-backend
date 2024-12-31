<?php

namespace App\Http\Requests\Admin\ModelTest;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateModelTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'package_id' => 'required|exists:packages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:3000',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'boolean',
            'pass_mark' => 'required|numeric|min:0', // Validation for pass_mark
            'full_mark' => 'required|numeric|min:0|gte:pass_mark', // Validation for full_mark, should be >= pass_mark
            'category' => 'array',
            'category.group_id' => [
                'nullable',
                'exists:groups,id',
                Rule::requiredIf(fn () => $this->hasAnyCategory()),
            ],
            'category.level_id' => [
                'nullable',
                Rule::exists('levels', 'id')->where(function ($query) {
                    $query->where('group_id', $this->input('category.group_id'));
                }),
                Rule::requiredIf(fn () => $this->input('category.subject_id') !== null),
            ],
            'category.subject_id' => [
                'nullable',
                Rule::exists('subjects', 'id')->where(function ($query) {
                    $query->where('level_id', $this->input('category.level_id'));
                }),
                Rule::requiredIf(fn () => $this->input('category.lesson_id') !== null),
            ],
            'category.lesson_id' => [
                'nullable',
                Rule::exists('lessons', 'id')->where(function ($query) {
                    $query->where('subject_id', $this->input('category.subject_id'));
                }),
                Rule::requiredIf(fn () => $this->input('category.topic_id') !== null),
            ],
            'category.topic_id' => [
                'nullable',
                Rule::exists('topics', 'id')->where(function ($query) {
                    $query->where('lesson_id', $this->input('category.lesson_id'));
                }),
                Rule::requiredIf(fn () => $this->input('category.sub_topic_id') !== null),
            ],
            'category.sub_topic_id' => [
                'nullable',
                Rule::exists('sub_topics', 'id')->where(function ($query) {
                    $query->where('topic_id', $this->input('category.topic_id'));
                }),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category.group_id.required' => 'The group field is required when any other category field is filled.',
            'category.level_id.required' => 'The level field is required when subject or any field after it is filled.',
            'category.subject_id.required' => 'The subject field is required when lesson or any field after it is filled.',
            'category.lesson_id.required' => 'The lesson field is required when topic or sub-topic is filled.',
            'category.topic_id.required' => 'The topic field is required when sub-topic is filled.',
            'category.*.exists' => 'The selected :attribute is invalid or does not belong to the selected parent category.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'category.group_id' => 'group',
            'category.level_id' => 'level',
            'category.subject_id' => 'subject',
            'category.lesson_id' => 'lesson',
            'category.topic_id' => 'topic',
            'category.sub_topic_id' => 'sub-topic',
        ];
    }

    /**
     * Check if any category field is filled.
     *
     * @return bool
     */
    private function hasAnyCategory(): bool
    {
        return $this->input('category.level_id') !== null
            || $this->input('category.subject_id') !== null
            || $this->input('category.lesson_id') !== null
            || $this->input('category.topic_id') !== null
            || $this->input('category.sub_topic_id') !== null;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages()));
    }
}
