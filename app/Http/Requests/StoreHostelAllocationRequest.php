<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHostelAllocationRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id',
            'hostel_id' => 'required|exists:hostels,id',
            'room_id' => 'required|exists:hostel_rooms,id',
            'hostel_fee_type_id' => 'nullable|exists:hostel_fee_types,id',
            'bed_number' => 'nullable|string',
            'allocation_date' => 'required|date',
            'remarks' => 'nullable|string',
        ];
    }
}
