<?php
namespace App\Http\Requests\Api\V1\Questions;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as needed based on your authorization logic
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
