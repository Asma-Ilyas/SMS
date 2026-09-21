<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentTransportRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'route_id' => 'required|exists:transport_routes,id',
            'route_stop_id' => 'nullable|exists:route_stops,id',
            'transport_fee_type_id' => 'nullable|exists:transport_fee_types,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'remarks' => 'nullable|string',
        ];
    }
}
