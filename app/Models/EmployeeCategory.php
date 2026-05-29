<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
    use HasFactory;

    protected $table = 'employee_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
        'arrival_time',
        'departure_time',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'arrival_time' => 'string',
        'departure_time' => 'string',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class); // adjust model name if needed (Staff/Employee)
    }
}