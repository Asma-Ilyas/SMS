<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransportRouteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $routeId = $this->route('transportRoute')?->id ?? $this->route('transport_route')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:transport_routes,code,' . $routeId,
            'start_point' => 'nullable|string',
            'end_point' => 'nullable|string',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_time_minutes' => 'nullable|integer|min:0',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ];
    }
}
