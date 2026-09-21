<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Shared helpers for every student-portal controller.
 * Resolves the logged-in user to a row in `students`:
 *   1) students.user_id = users.id   (preferred)
 *   2) students.email   = users.email (fallback)
 */
abstract class BaseStudentController extends Controller
{
    protected ?object $studentCache = null;

    protected const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    protected function student(): object
    {
        if ($this->studentCache) {
            return $this->studentCache;
        }

        $user = auth()->user();

        $base = fn () => DB::table('students as s')
            ->leftJoin('class_sections as cs', 'cs.id', '=', 's.class_section_id')
            ->leftJoin('classes as c', 'c.id', '=', 'cs.class_id')
            ->leftJoin('grades as g', 'g.id', '=', 'c.grade_id')
            ->leftJoin('streams as st', 'st.id', '=', 'c.stream_id')
            ->leftJoin('academic_sessions as ays', 'ays.id', '=', 'c.academic_session_id')
            ->select(
                's.*',
                'cs.section_name',
                'g.name as grade_name',
                'st.name as stream_name',
                'ays.name as session_name'
            );

        $student = null;

        if (Schema::hasColumn('students', 'user_id')) {
            $student = $base()->where('s.user_id', $user->id)->first();
        }

        if (!$student && !empty($user->email)) {
            $student = $base()->whereRaw('LOWER(s.email) = ?', [strtolower($user->email)])->first();
        }

        if (!$student) {
            throw new HttpResponseException(response()->view('student.no-profile', [], 404));
        }

        return $this->studentCache = $student;
    }

    /** Ownership guard: abort 404 if the row does not belong to this student. */
    protected function ensureOwner(?object $row, string $column = 'student_id'): void
    {
        abort_if(!$row || (int) $row->{$column} !== (int) $this->student()->id, 404);
    }

    protected function activeTimingId()
    {
        return DB::table('school_timings')->where('is_active', 1)->value('id');
    }

    protected function timetableQuery(int $sectionId)
    {
        $timingId = $this->activeTimingId();

        return DB::table('timetable_entries as te')
            ->join('time_slots as ts', 'ts.id', '=', 'te.time_slot_id')
            ->join('subjects as sub', 'sub.id', '=', 'te.subject_id')
            ->join('staff as t', 't.id', '=', 'te.teacher_id')
            ->join('rooms as r', 'r.id', '=', 'te.room_id')
            ->where('te.class_section_id', $sectionId)
            ->when($timingId, fn ($q) => $q->where('ts.school_timing_id', $timingId))
            ->select(
                'te.day_of_week',
                'ts.id as slot_id',
                'ts.label',
                'ts.start_time',
                'ts.end_time',
                'ts.sort_order',
                'sub.name as subject',
                'sub.code as subject_code',
                DB::raw("CONCAT(t.first_name,' ',t.last_name) as teacher"),
                'r.name as room',
                'r.room_number'
            );
    }

    /**
     * Day-level attendance summary (attendance is stored per subject per date,
     * so each date is collapsed into one day status).
     * Priority: present > late > on_duty > half_day > leave > holiday > absent
     */
    protected function attendanceSummary(int $studentId, ?string $from = null, ?string $to = null): array
    {
        $q = DB::table('student_attendance')->where('student_id', $studentId);
        if ($from) $q->whereDate('date', '>=', $from);
        if ($to)   $q->whereDate('date', '<=', $to);

        $byDate = $q->get(['date', 'status'])->groupBy(fn ($r) => substr((string) $r->date, 0, 10));

        $s = ['present' => 0, 'late' => 0, 'on_duty' => 0, 'half_day' => 0, 'leave' => 0, 'holiday' => 0, 'absent' => 0];

        foreach ($byDate as $rows) {
            $statuses = $rows->pluck('status')->all();
            foreach (['present', 'late', 'on_duty', 'half_day', 'leave', 'holiday'] as $p) {
                if (in_array($p, $statuses, true)) {
                    $s[$p]++;
                    continue 2;
                }
            }
            $s['absent']++;
        }

        $total    = $byDate->count();
        $working  = $total - $s['holiday'];
        $attended = $s['present'] + $s['late'] + $s['on_duty'] + 0.5 * $s['half_day'];

        return $s + [
            'total'      => $total,
            'working'    => $working,
            'percentage' => $working > 0 ? round($attended / $working * 100, 1) : 0,
        ];
    }
}
