<?php
namespace App\Http\Requests\Api\V1\Questions;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public function authorize()
    {
        return auth('admin')->check();
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'required|boolean',
        ];
    }
}
