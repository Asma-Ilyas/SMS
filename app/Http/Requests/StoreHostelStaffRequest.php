<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHostelStaffRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'hostel_id' => 'required|exists:hostels,id',
            'staff_id' => 'required|exists:staff,id',
            'role' => 'required|in:warden,deputy_warden,caretaker,security,other',
            'assigned_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ];
    }
}
