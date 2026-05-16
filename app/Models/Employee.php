<?php

// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['employee_id','name','email','phone','hire_date','basic_salary','position','department','bank_account','pan_number','pf_number','category_id'];
    
    // Accessor for daily rate (based on 26 working days)
    public function getDailyRateAttribute()
    {
        return round($this->basic_salary / 26, 2);
    }
    
    // Relationships
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }
    
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
    
    // Helper to get attendance summary for a given month
    public function attendanceSummary($year, $month)
    {
        $start = "$year-$month-01";
        $end = date("Y-m-t", strtotime($start));
        
        $attendances = $this->attendances()
            ->whereBetween('date', [$start, $end])
            ->get();
        
        $total_days = date('t', strtotime($start));
        $present = $attendances->whereIn('status', ['present','half_day'])->count();
        $absent = $attendances->where('status','absent')->count();
        $late = $attendances->where('status','late')->count();
        $half_days = $attendances->where('status','half_day')->count();
        
        return [
            'total_days'  => $total_days,
            'present'     => $present,
            'absent'      => $absent,
            'late'        => $late,
            'half_days'   => $half_days,
            'total_hours' => $attendances->sum('total_hours') ?? 0,
            'overtime'    => $attendances->sum('overtime') ?? 0,
        ];
    }

    public function category()
{
    return $this->belongsTo(EmployeeCategory::class, 'category_id');
}
}
