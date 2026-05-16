<?php
// app/Models/FeeSubmissionType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSubmissionType extends Model
{
    use HasFactory;

    protected $table = 'fee_submission_types';

    protected $fillable = [
        'name',
        'period',
        'amount',
        'description',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationship: this fee type can have many student submissions
    public function studentSubmissions()
    {
        return $this->hasMany(StudentFeeSubmission::class, 'fee_submission_type_id');
    }

    // Scope for active only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get period label
    public function getPeriodLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->period));
    }

    public function installments()
{
    return $this->hasMany(StudentFeeInstallment::class);
}
}