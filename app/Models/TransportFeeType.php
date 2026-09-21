<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportFeeType extends Model
{
    protected $fillable = [
        'name', 'route_id', 'period', 'amount', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
