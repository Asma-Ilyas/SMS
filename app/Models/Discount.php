<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'value', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function studentDiscounts()
    {
        return $this->hasMany(StudentDiscount::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function calculate($amount)
    {
        if ($this->type == 'percentage') {
            return round($amount * ($this->value / 100), 2);
        }
        return min($this->value, $amount); // fixed discount cannot exceed amount
    }

    public function classes()
{
    return $this->belongsToMany(Classes::class, 'discount_class');
}
}