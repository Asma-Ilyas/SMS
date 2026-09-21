<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Read-only school-wide timetable views for teachers. */
class TimetableReportController extends BaseTeacherController
{
    public function index()
    {
        $t = $this->teacher();
        $timingId = $this->activeTimingId();

        $stats = [
            'entries'  => DB::table('timetable_entries as te')->join('time_slots as ts', 'ts.id', '=', 'te.time_slot_id')
                ->when($timingId, fn ($q) => $q->where('ts.school_timing_id', $timingId))->count(),
            'sections' => DB::table('timetable_entries')->distinct()->count('class_section_id'),
            'teachers' => DB::table('timetable_entries')->distinct()->count('teacher_id'),
            'mine'     => DB::table('timetable_entries')->where('teacher_id', $t->id)->count(),
        ];
        $lastLog = DB::table('timetable_generation_logs')->orderByDesc('generated_at')->first();
        $timing = DB::table('school_timings')->where('is_active', 1)->first();

        return view('teacher.timetable-reports.index', compact('stats', 'lastLog', 'timing'));
    }

    public function byClass(Request $request)
    {
        $this->teacher();
        $classes = DB::table('classes as c')
            ->join('grades as g', 'g.id', '=', 'c.grade_id')
            ->join('streams as st', 'st.id', '=', 'c.stream_id')
            ->join('academic_sessions as ays', 'ays.id', '=', 'c.academic_session_id')
            ->orderByDesc('ays.is_active')->orderByDesc('ays.start_date')->orderBy('g.numeric_value')
            ->select('c.id', 'g.name as grade_name', 'st.name as stream_name', 'ays.name as session_name')->get();

        $days = self::DAYS; $slots = $this->slots(); $selected = null; $blocks = [];

        if ($request->filled('class_id')) {
            $selected = $classes->firstWhere('id', (int) $request->class_id);
            abort_if(!$selected, 404);
            $sections = DB::table('class_sections')->where('class_id', $selected->id)->orderBy('section_name')->get();
            foreach ($sections as $sec) {
                $rows = $this->ttBase()->where('te.class_section_id', $sec->id)->get();
                $blocks[] = ['title' => 'Section ' . $sec->section_name, 'grid' => $this->buildGrid($rows, 'section')];
            }
        }

        return view('teacher.timetable-reports.class', compact('classes', 'selected', 'blocks', 'days', 'slots'));
    }

    public function bySection(Request $request)
    {
        $this->teacher();
        $sections = DB::table('class_sections as cs')
            ->join('classes as c', 'c.id', '=', 'cs.class_id')
            ->join('grades as g', 'g.id', '=', 'c.grade_id')
            ->join('streams as st', 'st.id', '=', 'c.stream_id')
            ->join('academic_sessions as ays', 'ays.id', '=', 'c.academic_session_id')
            ->orderByDesc('ays.is_active')->orderBy('g.numeric_value')->orderBy('cs.section_name')
            ->select('cs.id', 'cs.section_name', 'g.name as grade_name', 'st.name as stream_name', 'ays.name as session_name')->get();

        $days = self::DAYS; $slots = $this->slots(); $selected = null; $grid = [];

        if ($request->filled('section_id')) {
            $selected = $sections->firstWhere('id', (int) $request->section_id);
            abort_if(!$selected, 404);
            $grid = $this->buildGrid($this->ttBase()->where('te.class_section_id', $selected->id)->get(), 'section');
        }

        return view('teacher.timetable-reports.section', compact('sections', 'selected', 'grid', 'days', 'slots'));
    }

    public function byTeacher(Request $request)
    {
        $me = $this->teacher();
        $teachers = DB::table('staff')->whereNull('deleted_at')->where('is_active', 1)
            ->where(function ($q) {
                $q->where('is_teacher', 1)->orWhereIn('id', DB::table('timetable_entries')->select('teacher_id'));
            })->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'designation']);

        $selectedId = (int) $request->get('teacher_id', $me->id);
        $selected = $teachers->firstWhere('id', $selectedId) ?? $teachers->firstWhere('id', $me->id);

        $days = self::DAYS; $slots = $this->slots(); $grid = [];
        if ($selected) {
            $grid = $this->buildGrid($this->ttBase()->where('te.teacher_id', $selected->id)->get(), 'teacher');
        }

        return view('teacher.timetable-reports.teacher', compact('teachers', 'selected', 'grid', 'days', 'slots', 'me'));
    }
}
