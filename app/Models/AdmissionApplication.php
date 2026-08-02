<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    protected $fillable = ['full_name', 'gender', 'email', 'phone', 'dob', 'city_campus', 'class'];
    protected $casts = ['dob' => 'date'];
}
