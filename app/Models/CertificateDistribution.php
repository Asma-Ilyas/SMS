<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CertificateDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_type_id',
        'student_id',
        'issue_date',
        'remarks',
        'certificate_file',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    /**
     * Public URL of the issued file, or null when none was uploaded.
     */
    public function getCertificateUrlAttribute(): ?string
    {
        return $this->certificate_file
            ? asset('storage/' . $this->certificate_file)
            : null;
    }

    public function certificateType(): BelongsTo
    {
        return $this->belongsTo(CertificateType::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Remove the stored file when the record is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $distribution) {
            if ($distribution->certificate_file) {
                Storage::disk('public')->delete($distribution->certificate_file);
            }
        });
    }
}