<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscriptionIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true if you want to allow any authenticated user to use this request
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
