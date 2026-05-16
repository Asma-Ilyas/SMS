<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'name' => 'required|string|unique:grades,name,' . $grade->id . '|max:10',
            'numeric_value' => 'required|integer|min:1|max:12|unique:grades,numeric_value,' . $grade->id,
        ];
    }
     public function messages(): array
    {
        return [
            'name.unique' => 'This grade name already exists.',
            'numeric_value.unique' => 'This numeric value is already in use.',
            'numeric_value.min' => 'Numeric value must be at least 1.',
            'numeric_value.max' => 'Numeric value cannot exceed 12.',
        ];
    }
}
