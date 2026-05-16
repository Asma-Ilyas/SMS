<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'certificate_number', 'certificate_types',
        'student_status_after', 'remarks', 'issued_date'
    ];

    protected $casts = [
        'certificate_types' => 'array',
        'issued_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public static function generateCertificateNumber()
    {
        $last = self::latest()->first();
        $num = $last ? intval(substr($last->certificate_number, -6)) + 1 : 1;
        return 'TC-' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }
}