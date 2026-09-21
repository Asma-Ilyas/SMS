<?php

namespace App\Http\Controllers\Teacher;

class TimetableController extends BaseTeacherController
{
    public function index()
    {
        $t = $this->teacher();
        $days  = self::DAYS;
        $slots = $this->slots();
        $rows  = $this->ttBase()->where('te.teacher_id', $t->id)->get();
        $grid  = $this->buildGrid($rows, 'teacher');
        $today = strtolower(now()->format('l'));

        $perDay = [];
        foreach ($days as $d) {
            $perDay[$d] = $rows->where('day_of_week', $d)->count();
        }

        return view('teacher.timetable.index', compact('t', 'days', 'slots', 'grid', 'today', 'perDay', 'rows'));
    }
}
