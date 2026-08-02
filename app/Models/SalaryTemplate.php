<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryTemplate extends Model
{
    use HasFactory;

    protected $table = 'salary_templates';

    protected $fillable = [
        'name',
        'description',
        'basic_salary',
        'allowances',
        'medical_allowance',
        'transport_allowance',
        'pf_percentage',
        'tax_percentage',
        'is_active'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'pf_percentage' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNetSalaryAttribute()
    {
        return $this->basic_salary + ($this->allowances ?? 0) + ($this->medical_allowance ?? 0) + ($this->transport_allowance ?? 0);
    }
}