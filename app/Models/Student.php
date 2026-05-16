<?php

namespace App\Models;
use App\Models\StudentFee;
use App\Models\AcademicSession;
use App\Models\Classes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

     protected $fillable = [
        // Personal
        'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth',
        'dob_in_words', 'religion', 'caste_subcaste', 'blood_group', 'address',
        'phone', 'email', 'city', 'state', 'country', 'extra_note', 'mother_tongue',
        'birth_place',
        // Previous School
        'previous_school_name', 'previous_school_address', 'previous_class',
        'passout_year', 'previous_category',
        // Admission
        'admission_date', 'student_type', 'class_id', 'section',
        'admission_number', 'roll_number', 'profile_photo',
        // Parent
        'father_name', 'father_phone', 'father_occupation', 'mother_name',
        'mother_phone', 'mother_occupation', 'parent_id_proof', 'parent_signature',
        // Concession
        'assigned_concession',
        // Status & Suspension
        'status', 'suspension_start_date', 'suspension_end_date', 'suspension_message',
    ];

   

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'suspension_start_date' => 'date',
        'suspension_end_date' => 'date',
    ];

    // Relationship with fees
    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }


    public function feeSubmissions()
{
    return $this->hasMany(StudentFeeSubmission::class);
}

public function feeInstallments()
{
    return $this->hasMany(StudentFeeInstallment::class);
}
    // Add this accessor
public function getProfilePhotoUrlAttribute()
{
    if ($this->profile_photo && file_exists(storage_path('app/public/' . $this->profile_photo))) {
        return asset('storage/' . $this->profile_photo);
    }
    return asset('images/default-avatar.png'); // place a default image in public/images
}

public function discounts()
{
    return $this->hasMany(StudentDiscount::class);
}


// Get the next grade (by numeric_value + 1)
public function getNextGrade()
{
    $currentGrade = $this->class->grade;
    $nextGrade = Grade::where('numeric_value', $currentGrade->numeric_value + 1)->first();
    return $nextGrade;
}

// Get the next class for the same academic session/stream/elective_track
public function getNextClass($academicSessionId = null)
{
    $sessionId = $academicSessionId ?? $this->class->academic_session_id;
    $nextGrade = $this->getNextGrade();
    if (!$nextGrade) return null;
    
    return Classes::where('academic_session_id', $sessionId)
        ->where('grade_id', $nextGrade->id)
        ->where('stream_id', $this->class->stream_id)
        ->where('elective_track_id', $this->class->elective_track_id)
        ->first();
}

public function transfers()
{
    return $this->hasMany(StudentTransfer::class);
}

public function transferCertificates()
{
    return $this->hasMany(TransferCertificate::class);
}

public function certificates()
{
    return $this->hasMany(CertificateDistribution::class);
}

public function invoices()
{
    return $this->hasMany(Invoice::class);
}
public function marks()
{
    return $this->hasMany(ExamMark::class, 'student_id');
}
}