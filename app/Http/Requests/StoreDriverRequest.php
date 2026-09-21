<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|unique:drivers,cnic',
            'license_number' => 'required|string|unique:drivers,license_number',
            'license_expiry' => 'nullable|date',
            'phone' => 'required|string|max:30',
            'emergency_contact' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'license_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'cnic_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'joining_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'remarks' => 'nullable|string',
        ];
    }
}
