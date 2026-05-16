<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    use HasFactory;

    protected $table = 'payroll_batches';

    protected $fillable = [
        'month',
        'run_date',
        'status',       // draft, processed, paid
        'total_employees',
        'total_amount',
        'created_by',
    ];

    protected $casts = [
        'run_date'      => 'datetime',
        'total_amount'  => 'decimal:2',
    ];

    /**
     * Get all salary records generated in this batch.
     */
    public function salaries()
    {
        return $this->hasMany(Salary::class, 'batch_id');
    }

    /**
     * Get the user (admin) who created this batch.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include batches for a given month.
     */
    public function scopeForMonth($query, $month)
    {
        return $query->where('month', $month);
    }
}