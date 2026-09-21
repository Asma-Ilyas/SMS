<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelFeeType extends Model
{
    protected $fillable = [
        'name', 'hostel_id', 'period', 'amount', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
