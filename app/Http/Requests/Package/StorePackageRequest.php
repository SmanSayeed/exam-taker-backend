<?php

namespace App\Http\Requests\Package;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use function Amp\Dns\query;

class StorePackageRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'price' => 'required|numeric',
            'duration_days' => 'required|numeric',
            'category' => 'required|array',
            'category.section_id' => [
                'nullable',
                'exists:sections,id',
                Rule::requiredIf(fn () => $this->hasAnyCategoryFields()),
            ],
            'category.exam_type_id' => [
                'nullable',
                Rule::exists('exam_types', 'id')->where(function ($query) {
                    $query->where('section_id', $this->input('category.section_id'));
                }),
                Rule::requiredIf(fn () => $this->input('category.exam_sub_type_id') !== null),
            ],
            'category.exam_sub_type_id' => [
                'nullable',
                Rule::exists('exam_sub_types', 'id')->where(function ($query) {
                    $query->where('exam_type_id', $this->input('category.exam_type_id'));
                })
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Validation errors occurred', 422, $errors->messages()));
    }

    public function messages()
    {

        return [
            'category.section_id.required' => ' The section field is required when any other category field is filled.',
            'category.exam_type_id.required' => 'The exam type field is required when any other category field is filled.',
            'category.exam_sub_type_id.required' => 'The exam sub type field is required when any other category field is filled.',
            'category.*.exists' => 'The selected :attribute is invalid or does not belong to the selected parent category.',
        ];
    }

    public function attributes()
    {
        return [
            'category.section_id' => 'section',
            'category.exam_type_id' => 'exam type',
            'category.exam_sub_type_id' => 'exam sub type',
        ];
    }

    /**
     * Check if any category-related fields are filled.
     *
     * @return bool
     */
    private function hasAnyCategoryFields(): bool
    {
        return $this->input('category.exam_type_id') !== null
            || $this->input('category.exam_sub_type_id') !== null;
    }
}
