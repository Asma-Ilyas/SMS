<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentTransportRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:transport_routes,id',
            'route_stop_id' => 'nullable|exists:route_stops,id',
            'transport_fee_type_id' => 'nullable|exists:transport_fee_types,id',
            'start_date' => 'required|date',
            'remarks' => 'nullable|string',
        ];
    }
}
