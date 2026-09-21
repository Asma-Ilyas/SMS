<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportRoute extends Model
{
    use SoftDeletes;

    protected $table = 'transport_routes';

    protected $fillable = [
        'name', 'code', 'start_point', 'end_point', 'distance_km',
        'estimated_time_minutes', 'vehicle_id', 'driver_id', 'is_active', 'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function stops()
    {
        return $this->hasMany(RouteStop::class, 'route_id')->orderBy('stop_order');
    }

    public function feeTypes()
    {
        return $this->hasMany(TransportFeeType::class, 'route_id');
    }

    public function studentTransports()
    {
        return $this->hasMany(StudentTransport::class, 'route_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
