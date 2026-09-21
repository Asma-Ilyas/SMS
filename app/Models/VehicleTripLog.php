<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleTripLog extends Model
{
    protected $fillable = [
        'vehicle_id', 'route_id', 'driver_id', 'trip_date', 'trip_type',
        'start_time', 'end_time', 'start_latitude', 'start_longitude',
        'end_latitude', 'end_longitude', 'status', 'notes',
    ];

    protected $casts = [
        'trip_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
