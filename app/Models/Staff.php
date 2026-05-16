<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';
    protected $fillable = [
        'first_name', 'last_name', 'father_name', 'mother_name', 'gender', 'date_of_birth',
        'cnic', 'passport_number', 'nationality', 'religion', 'email', 'phone', 'mobile',
        'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone',
        'present_address', 'permanent_address', 'city', 'state', 'postal_code', 'country',
        'employee_id','category_id', 'designation', 'department', 'employment_type', 'joining_date',
        'confirmation_date', 'resignation_date', 'contract_end_date', 'qualification',
        'experience', 'specialization', 'basic_salary', 'bank_name', 'bank_account_number',
        'bank_iban', 'tax_number', 'profile_photo', 'cnic_front_image', 'cnic_back_image',
        'resume', 'degree_certificate', 'is_active', 'remarks', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'confirmation_date' => 'date',
        'resignation_date' => 'date',
        'contract_end_date' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject_teacher', 'teacher_id', 'subject_id')
                    ->withPivot('class_id', 'academic_session_id', 'max_weekly_periods', 'term')
                    ->withTimestamps();
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subject_teacher', 'teacher_id', 'class_id')
                    ->withPivot('subject_id', 'academic_session_id', 'max_weekly_periods', 'term')
                    ->withTimestamps();
    }

    public function category()
{
    return $this->belongsTo(EmployeeCategory::class, 'category_id');
}

public function getNameAttribute()
{
    return $this->first_name . ' ' . $this->last_name;
}

public function attendances()
{
    return $this->hasMany(Attendance::class, 'employee_id');
}

public function leaves()
{
    return $this->hasMany(Leave::class, 'employee_id');
}

public function salaries()
{
    return $this->hasMany(Salary::class, 'employee_id');
}
}