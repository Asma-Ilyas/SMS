<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStreamRequest extends FormRequest
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
            'name' => 'required|string|unique:streams,name|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This stream name already exists.',
            'name.max' => 'Stream name cannot exceed 20 characters.',
        ];
    }
}
