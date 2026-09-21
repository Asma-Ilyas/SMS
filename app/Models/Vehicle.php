<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_number', 'type', 'model', 'manufacturer', 'manufacture_year',
        'seating_capacity', 'registration_number', 'registration_expiry',
        'insurance_expiry', 'fitness_expiry', 'driver_id', 'status', 'photo', 'notes',
    ];

    protected $casts = [
        'registration_expiry' => 'date',
        'insurance_expiry' => 'date',
        'fitness_expiry' => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function routes()
    {
        return $this->hasMany(TransportRoute::class);
    }

    public function tripLogs()
    {
        return $this->hasMany(VehicleTripLog::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(VehicleMaintenanceLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
