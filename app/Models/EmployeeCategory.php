<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
    protected $table = 'employee_categories';
    protected $fillable = ['name', 'code', 'description', 'arrival_time', 'departure_time', 'is_active'];
    public function staff() { return $this->hasMany(Staff::class, 'category_id'); }
}