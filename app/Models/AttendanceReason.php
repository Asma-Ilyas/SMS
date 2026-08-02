<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReason extends Model
{
    use HasFactory;

    protected $table = 'attendance_reasons';

    protected $fillable = [
        'category',
        'reason',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Categories
    const CATEGORY_HALF_DAY = 'half_day';
    const CATEGORY_LATE = 'late';
    const CATEGORY_ABSENT = 'absent';

    /**
     * Get all category options
     */
    public static function getCategoryOptions()
    {
        return [
            self::CATEGORY_HALF_DAY => 'Half Day',
            self::CATEGORY_LATE => 'Late',
            self::CATEGORY_ABSENT => 'Absent',
        ];
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        return self::getCategoryOptions()[$this->category] ?? ucfirst($this->category);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeHalfDayReasons($query)
    {
        return $query->where('category', self::CATEGORY_HALF_DAY);
    }

    public function scopeLateReasons($query)
    {
        return $query->where('category', self::CATEGORY_LATE);
    }

    public function scopeAbsentReasons($query)
    {
        return $query->where('category', self::CATEGORY_ABSENT);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('reason');
    }

    /**
     * Get reasons by category as array for dropdown
     */
    public static function getReasonsForDropdown($category)
    {
        return self::where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('reason', 'id')
            ->toArray();
    }
}