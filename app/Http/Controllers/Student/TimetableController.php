<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;

class TimetableController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();
        $days = self::DAYS;

        $slots = collect();
        $grid  = [];

        if ($s->class_section_id) {
            $timingId = $this->activeTimingId();

            $slots = DB::table('time_slots')
                ->where('is_active', 1)
                ->when($timingId, fn ($q) => $q->where('school_timing_id', $timingId))
                ->orderBy('sort_order')->get();

            foreach ($this->timetableQuery($s->class_section_id)->get() as $e) {
                $grid[$e->day_of_week][$e->slot_id] = $e;
            }
        }

        $todayName = strtolower(now()->format('l'));

        return view('student.timetable.index', compact('s', 'days', 'slots', 'grid', 'todayName'));
    }
}
