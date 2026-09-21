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
        'employee_id', 'category_id', 'designation', 'department', 'employment_type', 'joining_date',
        'confirmation_date', 'resignation_date', 'contract_end_date', 'qualification',
        'experience', 'specialization', 'basic_salary', 'bank_name', 'bank_account_number',
        'bank_iban', 'tax_number', 'profile_photo', 'cnic_front_image', 'cnic_back_image',
        'resume', 'degree_certificate', 'is_active', 'is_teacher', 'remarks', 'created_by', 
        'updated_by', 'attendance_required', 'salary_type', 'hourly_rate', 'daily_rate',
        'allowances', 'pf_percentage', 'medical_allowance', 'transport_allowance'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'confirmation_date' => 'date',
        'resignation_date' => 'date',
        'contract_end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'allowances' => 'decimal:2',
        'pf_percentage' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
    ];

    // ========== RELATIONSHIPS ==========

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'teacher_id');
    }

    /**
     * Get subjects directly through teacherSubjects
     */
    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            TeacherSubject::class,
            'teacher_id', // Foreign key on teacher_subjects table
            'id', // Foreign key on subjects table
            'id', // Local key on staff table
            'subject_id' // Local key on teacher_subjects table
        );
    }
    

    public function teacherAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::class, 'teacher_id');
    }

    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class, 'staff_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class, 'staff_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'staff_id');
    }

    public function category()
    {
        return $this->belongsTo(EmployeeCategory::class, 'category_id');
    }

    public function classAttendances()
    {
        return $this->hasMany(TeacherClassAttendance::class, 'teacher_id');
    }

    public function attendanceSummaries()
    {
        return $this->hasMany(AttendanceSummary::class, 'staff_id');
    }

    // ========== ACCESSORS ==========

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameWithEmployeeIdAttribute()
    {
        return "{$this->full_name} ({$this->employee_id})";
    }

    public function getMonthlySalaryAttribute()
    {
        return $this->basic_salary;
    }

    public function getDailySalaryAttribute()
    {
        if ($this->daily_rate > 0) {
            return $this->daily_rate;
        }
        return $this->basic_salary > 0 ? round($this->basic_salary / 30, 2) : 0;
    }

    public function getHourlySalaryAttribute()
    {
        if ($this->hourly_rate > 0) {
            return $this->hourly_rate;
        }
        $daily = $this->daily_salary;
        return $daily > 0 ? round($daily / 8, 2) : 0;
    }

    public function getAttendancePercentageAttribute()
    {
        $totalDays = $this->attendances()->count();
        if ($totalDays == 0) return 0;
        
        $presentDays = $this->attendances()->where('status', 'present')->count();
        return round(($presentDays / $totalDays) * 100, 2);
    }

    public function getTotalClassesTakenAttribute()
    {
        return $this->attendances()->where('class_taken', true)->count();
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // ========== SCOPES ==========

    public function scopeTeachers($query)
    {
        return $query->where('is_teacher', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('employee_id', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }
    public function getNameAttribute(): string
{
    return trim($this->first_name . ' ' . $this->last_name);
}
}