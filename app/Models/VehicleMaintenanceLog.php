<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMaintenanceLog extends Model
{
    protected $fillable = [
        'vehicle_id', 'maintenance_date', 'type', 'description', 'cost', 'next_due_date',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
