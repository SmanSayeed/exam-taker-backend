<?php

namespace App\Http\Requests\PackagePlans;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePackagePlanRequest extends FormRequest
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
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
