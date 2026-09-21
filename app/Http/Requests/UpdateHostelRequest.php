<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHostelRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $hostelId = $this->route('hostel')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hostels,code,' . $hostelId,
            'type' => 'required|in:boys,girls,mixed',
            'address' => 'nullable|string',
            'warden_id' => 'nullable|exists:staff,id',
            'total_capacity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
