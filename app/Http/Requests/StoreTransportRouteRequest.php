<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransportRouteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:transport_routes,code',
            'start_point' => 'nullable|string',
            'end_point' => 'nullable|string',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_time_minutes' => 'nullable|integer|min:0',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'stops' => 'nullable|array',
            'stops.*.stop_name' => 'required_with:stops|string',
            'stops.*.pickup_time' => 'nullable',
            'stops.*.drop_time' => 'nullable',
            'stops.*.latitude' => 'nullable|numeric|between:-90,90',
            'stops.*.longitude' => 'nullable|numeric|between:-180,180',
        ];
    }
}
