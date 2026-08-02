<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['name', 'type', 'value', 'description', 'is_active'];
    protected $casts = ['value' => 'decimal:2', 'is_active' => 'boolean'];

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
        if ($this->type === 'percentage') {
            return round($amount * ($this->value / 100), 2);
        }
        return min($this->value, $amount);
    }
}