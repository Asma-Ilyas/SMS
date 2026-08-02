<?php

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
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active fee types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get period label
     */
    public function getPeriodLabelAttribute()
    {
        $labels = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annually' => 'Annually',
            'one_time' => 'One Time',
        ];
        return $labels[$this->period] ?? ucfirst($this->period);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? '✅ Active' : '❌ Inactive';
    }
}