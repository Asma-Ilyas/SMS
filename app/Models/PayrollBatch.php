<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollBatch extends Model
{
    use HasFactory;

    protected $table = 'payroll_batches';

    protected $fillable = [
        'batch_number', 'month', 'run_date', 'status', 'total_amount',
        'total_employees', 'processed_by', 'processed_at', 'approved_by',
        'approved_at', 'notes'
    ];

    protected $casts = [
        'run_date' => 'date',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function processedBy()
    {
        return $this->belongsTo(Staff::class, 'processed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }
}