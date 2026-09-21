<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransport extends Model
{
    protected $fillable = [
        'student_id', 'route_id', 'route_stop_id', 'vehicle_id',
        'transport_fee_type_id', 'start_date', 'end_date', 'status', 'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function stop()
    {
        return $this->belongsTo(RouteStop::class, 'route_stop_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function feeType()
    {
        return $this->belongsTo(TransportFeeType::class, 'transport_fee_type_id');
    }

    public function feePayments()
    {
        return $this->hasMany(StudentTransportFeePayment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function routeStop()
{
    return $this->belongsTo(RouteStop::class, 'route_stop_id');
}
}
