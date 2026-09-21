<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'vehicle_number' => 'required|string|unique:vehicles,vehicle_number,' . $vehicleId,
            'type' => 'required|in:bus,van,coaster,car',
            'model' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'manufacture_year' => 'nullable|digits:4|integer|min:1980|max:' . (date('Y') + 1),
            'seating_capacity' => 'required|integer|min:1|max:200',
            'registration_number' => 'nullable|string',
            'registration_expiry' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'fitness_expiry' => 'nullable|date',
            'driver_id' => 'nullable|exists:drivers,id',
            'status' => 'nullable|in:active,maintenance,inactive',
            'photo' => 'nullable|image|max:2048',
            'registration_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'insurance_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'fitness_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string',
        ];
    }
}
