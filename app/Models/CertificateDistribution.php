<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateDistribution extends Model
{
    use HasFactory;

   // Add to $fillable
protected $fillable = ['certificate_type_id', 'student_id', 'issue_date', 'remarks', 'certificate_file'];


    protected $casts = [
        'issue_date' => 'date',
    ];

    public function getCertificateUrlAttribute()
{
    return $this->certificate_file ? asset('storage/' . $this->certificate_file) : null;
}

    public function certificateType()
    {
        return $this->belongsTo(CertificateType::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}