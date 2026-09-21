<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHostelRoomRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'hostel_id' => 'required|exists:hostels,id',
            'room_type_id' => 'nullable|exists:hostel_room_types,id',
            'room_number' => [
                'required', 'string',
                \Illuminate\Validation\Rule::unique('hostel_rooms')->where(fn ($q) => $q->where('hostel_id', $this->hostel_id)),
            ],
            'floor' => 'nullable|string',
            'capacity' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string',
        ];
    }
}
