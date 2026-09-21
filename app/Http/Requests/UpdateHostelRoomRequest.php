<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHostelRoomRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $roomId = $this->route('hostelRoom')?->id ?? $this->route('hostel_room')?->id;

        return [
            'hostel_id' => 'required|exists:hostels,id',
            'room_type_id' => 'nullable|exists:hostel_room_types,id',
            'room_number' => [
                'required', 'string',
                \Illuminate\Validation\Rule::unique('hostel_rooms')->where(fn ($q) => $q->where('hostel_id', $this->hostel_id))->ignore($roomId),
            ],
            'floor' => 'nullable|string',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,full,maintenance',
            'notes' => 'nullable|string',
        ];
    }
}
