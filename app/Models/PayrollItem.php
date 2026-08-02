<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $table = 'payroll_items';

    protected $fillable = [
        'payroll_batch_id', 'salary_id', 'staff_id', 'amount', 'status', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payrollBatch()
    {
        return $this->belongsTo(PayrollBatch::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}