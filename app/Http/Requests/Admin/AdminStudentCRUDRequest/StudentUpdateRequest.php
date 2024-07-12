<?php

namespace App\Http\Requests\Admin\AdminStudentCRUDRequest;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
class StudentUpdateRequest extends FormRequest
{

    public function authorize()
    {
        // Check if the authenticated user is an admin
        return Auth::guard('admin-api')->check();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'phone' => 'nullable|string|max:20|unique:students',
            'profile_image' => 'nullable|image|max:2048',
            'country' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|max:2',
            'address' => 'nullable|string|max:500',
            'active_status' => 'boolean',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error('Update validation errors occurred', 422, $errors->messages()));
    }
}
