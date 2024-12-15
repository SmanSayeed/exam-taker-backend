<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PdfSubscriptionIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_active' => 'in:true,false',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        // Use ApiResponseHelper for JSON response
        throw new HttpResponseException(ApiResponseHelper::error(' validation errors occurred', 422, $errors->messages()));
    }
}
