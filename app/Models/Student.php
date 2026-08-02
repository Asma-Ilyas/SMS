<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    // use SoftDeletes; // Uncomment if you want soft deletes

    protected $table = 'students';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'dob_in_words',
        'religion',
        'caste_subcaste',
        'blood_group',
        'address',
        'phone',
        'email',
        'city',
        'state',
        'country',
        'extra_note',
        'mother_tongue',
        'birth_place',
        'previous_school_name',
        'previous_school_address',
        'previous_class',
        'passout_year',
        'previous_category',
        'admission_date',
        'student_type',
        'admission_number',
        'roll_number',
        'profile_photo',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'parent_id_proof',
        'parent_signature',
        'assigned_concession',
        'status',
        'suspension_start_date',
        'suspension_end_date',
        'suspension_message',
        'class_section_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'suspension_start_date' => 'date',
        'suspension_end_date' => 'date',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the class section for this student
     */
    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    /**
     * Get the class through class section
     */
    public function class()
    {
        return $this->hasOneThrough(
            Classes::class,
            ClassSection::class,
            'id',          // Foreign key on class_sections table
            'id',          // Foreign key on classes table
            'class_section_id', // Local key on students table
            'class_id'     // Local key on class_sections table
        );
    }

    /**
     * Get the grade through class section
     */
    public function grade()
    {
        return $this->hasOneThrough(
            Grade::class,
            ClassSection::class,
            'id',
            'id',
            'class_section_id',
            'class_id'
        );
    }

    /**
     * Get the stream through class section
     */
    public function stream()
    {
        return $this->hasOneThrough(
            Stream::class,
            ClassSection::class,
            'id',
            'id',
            'class_section_id',
            'class_id'
        );
    }

    /**
     * Get all attendance records for this student
     */
    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    /**
     * Get latest attendance records
     */
    public function latestAttendances($limit = 10)
    {
        return $this->attendances()->latest()->limit($limit);
    }

    /**
     * Get exam marks for this student
     */
    public function examMarks()
    {
        return $this->hasMany(ExamMark::class);
    }

    /**
     * Get exam results for this student
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Get fee submissions for this student
     */
    public function feeSubmissions()
    {
        return $this->hasMany(StudentFeeSubmission::class);
    }

    /**
     * Alias for feeSubmissions (for backward compatibility)
     */
    public function feeInstallments()
    {
        return $this->feeSubmissions();
    }

    /**
     * Get invoices for this student
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get discounts for this student
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'student_discounts')
                    ->withPivot('fee_submission_type_id', 'valid_from', 'valid_until')
                    ->withTimestamps();
    }

    /**
     * Get certificates for this student
     */
    public function certificates()
    {
        return $this->hasMany(CertificateDistribution::class);
    }

    /**
     * Get transfers for this student
     */
    public function transfers()
    {
        return $this->hasMany(StudentTransfer::class);
    }

    /**
     * Get transfer certificates for this student
     */
    public function transferCertificates()
    {
        return $this->hasMany(TransferCertificate::class);
    }

    // ========== ACCESSORS ==========

    /**
     * Get the student's full name
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Get the student's full name with admission number
     */
    public function getFullNameWithAdmissionAttribute()
    {
        return "{$this->full_name} ({$this->admission_number})";
    }

    /**
     * Get the student's age
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get the student's current class
     */
    public function getCurrentClassAttribute()
    {
        if (!$this->classSection) {
            return 'N/A';
        }
        
        $class = $this->classSection->class;
        if (!$class) {
            return 'N/A';
        }
        
        $gradeName = $class->grade->name ?? 'N/A';
        $streamName = $class->stream->name ?? '';
        $sectionName = $this->classSection->section_name ?? '';
        
        if ($streamName && !in_array($gradeName, ['KG', '1', '2', '3', '4', '5'])) {
            return $gradeName . ' ' . $streamName . ' - ' . $sectionName;
        }
        
        return $gradeName . ' - ' . $sectionName;
    }

    /**
     * Get attendance percentage
     */
    public function getAttendancePercentageAttribute()
    {
        $totalDays = $this->attendances()->count();
        if ($totalDays == 0) return 0;
        
        $presentDays = $this->attendances()->where('status', 'present')->count();
        $lateDays = $this->attendances()->where('status', 'late')->count();
        $halfDayDays = $this->attendances()->where('status', 'half_day')->count();
        $attended = $presentDays + $lateDays + $halfDayDays;
        
        return round(($attended / $totalDays) * 100, 2);
    }

    /**
     * Get overall exam percentage
     */
    public function getOverallPercentageAttribute()
    {
        $results = $this->examResults;
        if ($results->isEmpty()) return 0;
        
        $totalPercentage = $results->sum('percentage');
        return round($totalPercentage / $results->count(), 2);
    }

    /**
     * Get overall grade
     */
    public function getOverallGradeAttribute()
    {
        $percentage = $this->overall_percentage;
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Active' => 'success',
            'Inactive' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get fee summary
     */
    public function getFeeSummaryAttribute()
    {
        $feeSubmissions = $this->feeSubmissions;
        return [
            'total' => $feeSubmissions->sum('amount'),
            'paid' => $feeSubmissions->where('status', 'paid')->sum('paid_amount'),
            'pending' => $feeSubmissions->whereIn('status', ['pending', 'partial'])->sum('amount'),
            'overdue' => $feeSubmissions->where('status', 'pending')->where('due_date', '<', now())->count(),
            'total_installments' => $feeSubmissions->count(),
            'paid_installments' => $feeSubmissions->where('status', 'paid')->count(),
            'pending_installments' => $feeSubmissions->where('status', 'pending')->count(),
            'partial_installments' => $feeSubmissions->where('status', 'partial')->count(),
        ];
    }

    /**
     * Get exam performance summary
     */
    public function getExamPerformanceAttribute()
    {
        $results = $this->examResults()->with('exam')->orderBy('created_at', 'desc')->take(5)->get();
        return $results;
    }

    // ========== SCOPES ==========

    /**
     * Scope for active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for inactive students
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    /**
     * Scope for students in a specific class
     */
    public function scopeByClass($query, $classSectionId)
    {
        return $query->where('class_section_id', $classSectionId);
    }

    /**
     * Scope for students by gender
     */
    public function scopeMale($query)
    {
        return $query->where('gender', 'Male');
    }

    /**
     * Scope for students by gender
     */
    public function scopeFemale($query)
    {
        return $query->where('gender', 'Female');
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('admission_number', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('father_name', 'LIKE', "%{$search}%")
              ->orWhere('admission_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope for students born after a date
     */
    public function scopeBornAfter($query, $date)
    {
        return $query->where('date_of_birth', '>', $date);
    }

    /**
     * Scope for students born before a date
     */
    public function scopeBornBefore($query, $date)
    {
        return $query->where('date_of_birth', '<', $date);
    }

    /**
     * Scope for students with fee pending
     */
    public function scopeWithFeePending($query)
    {
        return $query->whereHas('feeSubmissions', function($q) {
            $q->whereIn('status', ['pending', 'partial']);
        });
    }

    // ========== HELPERS ==========

    /**
     * Check if student is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Check if student is suspended
     */
    public function isSuspended()
    {
        return $this->suspension_start_date && 
               $this->suspension_end_date && 
               now()->between($this->suspension_start_date, $this->suspension_end_date);
    }

    /**
     * Check if student is currently suspended
     */
    public function isCurrentlySuspended()
    {
        return $this->isSuspended();
    }

    /**
     * Get today's attendance
     */
    public function getTodayAttendanceAttribute()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }

    /**
     * Get attendance for a specific date
     */
    public function getAttendanceForDate($date)
    {
        return $this->attendances()->whereDate('date', $date)->first();
    }

    /**
     * Calculate attendance for a date range
     */
    public function getAttendanceBetween($startDate, $endDate)
    {
        return $this->attendances()
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();
    }

    /**
     * Get fee submissions for a specific status
     */
    public function getFeeSubmissionsByStatus($status)
    {
        return $this->feeSubmissions()->where('status', $status)->get();
    }

    /**
     * Get total fee paid
     */
    public function getTotalFeePaidAttribute()
    {
        return $this->feeSubmissions()->where('status', 'paid')->sum('paid_amount');
    }

    /**
     * Get total fee pending
     */
    public function getTotalFeePendingAttribute()
    {
        $total = $this->feeSubmissions()->sum('amount');
        $paid = $this->feeSubmissions()->where('status', 'paid')->sum('paid_amount');
        return $total - $paid;
    }

    /**
     * Check if student has any pending fee
     */
    public function hasPendingFee()
    {
        return $this->feeSubmissions()
                    ->whereIn('status', ['pending', 'partial'])
                    ->exists();
    }

    /**
     * Get student's rank in class for a specific exam
     */
    public function getRankInExam($examId)
    {
        $result = $this->examResults()->where('exam_id', $examId)->first();
        return $result ? $result->rank_in_class : null;
    }

    /**
     * Get student's grade for a specific exam
     */
    public function getGradeInExam($examId)
    {
        $result = $this->examResults()->where('exam_id', $examId)->first();
        return $result ? $result->grade : null;
    }
}