<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'cnic', 'license_number', 'license_expiry', 'phone',
        'emergency_contact', 'address', 'photo', 'joining_date',
        'is_active', 'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'license_expiry' => 'date',
        'joining_date' => 'date',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function routes()
    {
        return $this->hasMany(TransportRoute::class, 'driver_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
