<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'employee_id', // foreign key to staff.id
        'date',
        'check_in',
        'check_out',
        'total_hours',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date'      => 'date',
        'check_in'  => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
    ];

   // app/Models/Attendance.php

public function staff()
{
    return $this->belongsTo(Staff::class, 'staff_id');
}

// Optional: keep an alias for backward compatibility
public function employee()
{
    return $this->belongsTo(Staff::class, 'staff_id');
}

    // Auto-calculate total hours before saving
    protected static function booted()
    {
        static::saving(function ($attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $checkIn  = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $diffMinutes = $checkOut->diffInMinutes($checkIn);
                $attendance->total_hours = round($diffMinutes / 60, 2);
            }
        });
    }
}