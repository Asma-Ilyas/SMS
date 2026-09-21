<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Shared helpers for every teacher-portal controller.
 * Resolves the logged-in user to a row in `staff`:
 *   1) staff.user_id = users.id    (preferred)
 *   2) staff.email   = users.email (fallback)
 */
abstract class BaseTeacherController extends Controller
{
    protected ?object $teacherCache = null;
    protected ?array $gradeScaleCache = null;

    protected const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    // ------------------------------------------------------------------
    // Identity
    // ------------------------------------------------------------------
    protected function teacher(): object
    {
        if ($this->teacherCache) {
            return $this->teacherCache;
        }

        $user = auth()->user();

        $base = fn () => DB::table('staff as s')
            ->leftJoin('employee_categories as ec', 'ec.id', '=', 's.category_id')
            ->whereNull('s.deleted_at')
            ->select('s.*', 'ec.name as category_name', 'ec.arrival_time as cat_arrival', 'ec.departure_time as cat_departure');

        $teacher = null;

        if (Schema::hasColumn('staff', 'user_id')) {
            $teacher = $base()->where('s.user_id', $user->id)->first();
        }

        if (!$teacher && !empty($user->email)) {
            $teacher = $base()->whereRaw('LOWER(s.email) = ?', [strtolower($user->email)])->first();
        }

        if (!$teacher) {
            throw new HttpResponseException(response()->view('teacher.no-profile', [], 404));
        }

        return $this->teacherCache = $teacher;
    }

    protected function ensureOwner(?object $row, string $column = 'staff_id'): void
    {
        abort_if(!$row || (int) $row->{$column} !== (int) $this->teacher()->id, 404);
    }

    // ------------------------------------------------------------------
    // Teaching assignments  (section + subject pairs)
    // ------------------------------------------------------------------
    /**
     * @param bool $forAttendance also include pairs granted through attendance_teacher_section
     */
    protected function pairs(bool $forAttendance = false): Collection
    {
        $tid = $this->teacher()->id;
        $raw = collect();

        foreach (DB::table('subject_assignments')->where('teacher_id', $tid)->where('is_active', 1)->get(['class_section_id', 'subject_id']) as $r) {
            $raw->push([(int) $r->class_section_id, (int) $r->subject_id]);
        }
        foreach (DB::table('timetable_entries')->where('teacher_id', $tid)->distinct()->get(['class_section_id', 'subject_id']) as $r) {
            $raw->push([(int) $r->class_section_id, (int) $r->subject_id]);
        }

        if ($forAttendance && Schema::hasTable('attendance_teacher_section')) {
            foreach (DB::table('attendance_teacher_section')->where('teacher_id', $tid)->where('can_mark_attendance', 1)->get() as $p) {
                if ($p->subject_id) {
                    $raw->push([(int) $p->class_section_id, (int) $p->subject_id]);
                } else {
                    foreach (DB::table('subject_assignments')->where('class_section_id', $p->class_section_id)->where('is_active', 1)->pluck('subject_id') as $sid) {
                        $raw->push([(int) $p->class_section_id, (int) $sid]);
                    }
                }
            }
        }

        $raw = $raw->unique(fn ($p) => $p[0] . '|' . $p[1])->values();
        if ($raw->isEmpty()) {
            return collect();
        }

        $sections = DB::table('class_sections as cs')
            ->join('classes as c', 'c.id', '=', 'cs.class_id')
            ->join('grades as g', 'g.id', '=', 'c.grade_id')
            ->join('streams as st', 'st.id', '=', 'c.stream_id')
            ->join('academic_sessions as ays', 'ays.id', '=', 'c.academic_session_id')
            ->whereIn('cs.id', $raw->pluck(0)->unique()->all())
            ->select('cs.id', 'cs.section_name', 'g.name as grade_name', 'st.name as stream_name',
                     'ays.name as session_name', 'ays.is_active as session_active', 'c.id as class_id')
            ->get()->keyBy('id');

        $subjects = DB::table('subjects')->whereIn('id', $raw->pluck(1)->unique()->all())
            ->get(['id', 'name', 'code'])->keyBy('id');

        $out = $raw->map(function ($p) use ($sections, $subjects) {
            $s   = $sections[$p[0]] ?? null;
            $sub = $subjects[$p[1]] ?? null;
            if (!$s || !$sub) {
                return null;
            }
            $sectionLabel = 'Class ' . $s->grade_name . '-' . $s->section_name . ($s->stream_name ? ' (' . $s->stream_name . ')' : '');
            return (object) [
                'key'            => $p[0] . '|' . $p[1],
                'section_id'     => $p[0],
                'subject_id'     => $p[1],
                'class_id'       => $s->class_id,
                'section_label'  => $sectionLabel,
                'subject_name'   => $sub->name,
                'subject_code'   => $sub->code,
                'session_name'   => $s->session_name,
                'session_active' => (bool) $s->session_active,
                'label'          => $sectionLabel . ' · ' . $sub->name,
            ];
        })->filter()->values();

        // Hide old-session assignments once an active session has assignments
        if ($out->contains('session_active', true)) {
            $out = $out->where('session_active', true)->values();
        }

        return $out->sortBy('label')->values();
    }

    protected function mySections(bool $forAttendance = false): Collection
    {
        return $this->pairs($forAttendance)->unique('section_id')
            ->map(fn ($p) => (object) ['id' => $p->section_id, 'label' => $p->section_label])->values();
    }

    /** Find a pair the teacher owns or abort 403. */
    protected function findPair(int $sectionId, int $subjectId, bool $forAttendance = false): object
    {
        $pair = $this->pairs($forAttendance)->first(fn ($p) => $p->section_id === $sectionId && $p->subject_id === $subjectId);
        abort_if(!$pair, 403, 'You are not assigned to this class/subject.');
        return $pair;
    }

    protected function studentsOfSection(int $sectionId): Collection
    {
        return DB::table('students')
            ->where('class_section_id', $sectionId)->where('status', 'Active')
            ->orderByRaw('CAST(roll_number AS UNSIGNED)')->orderBy('first_name')->get();
    }

    // ------------------------------------------------------------------
    // Timetable helpers
    // ------------------------------------------------------------------
    protected function activeTimingId()
    {
        return DB::table('school_timings')->where('is_active', 1)->value('id');
    }

    protected function ttBase()
    {
        $timingId = $this->activeTimingId();

        return DB::table('timetable_entries as te')
            ->join('time_slots as ts', 'ts.id', '=', 'te.time_slot_id')
            ->join('subjects as sub', 'sub.id', '=', 'te.subject_id')
            ->join('staff as t', 't.id', '=', 'te.teacher_id')
            ->join('rooms as r', 'r.id', '=', 'te.room_id')
            ->join('class_sections as cs', 'cs.id', '=', 'te.class_section_id')
            ->join('classes as c', 'c.id', '=', 'cs.class_id')
            ->join('grades as g', 'g.id', '=', 'c.grade_id')
            ->when($timingId, fn ($q) => $q->where('ts.school_timing_id', $timingId))
            ->select(
                'te.day_of_week', 'te.class_section_id', 'te.subject_id',
                'ts.id as slot_id', 'ts.label', 'ts.start_time', 'ts.end_time', 'ts.sort_order',
                'sub.name as subject',
                DB::raw("CONCAT(t.first_name,' ',t.last_name) as teacher"),
                'r.name as room', 'r.room_number',
                'g.name as grade_name', 'cs.section_name'
            );
    }

    protected function slots(): Collection
    {
        $timingId = $this->activeTimingId();
        return DB::table('time_slots')->where('is_active', 1)
            ->when($timingId, fn ($q) => $q->where('school_timing_id', $timingId))
            ->orderBy('sort_order')->get();
    }

    /** mode: 'section' (line2 = teacher) | 'teacher' (line2 = class) */
    protected function buildGrid(Collection $rows, string $mode = 'section'): array
    {
        $grid = [];
        foreach ($rows as $e) {
            $grid[$e->day_of_week][$e->slot_id] = [
                'l1' => $e->subject,
                'l2' => $mode === 'teacher' ? 'Class ' . $e->grade_name . '-' . $e->section_name : $e->teacher,
                'l3' => 'Room ' . ($e->room_number ?? $e->room),
            ];
        }
        return $grid;
    }

    // ------------------------------------------------------------------
    // Marks helpers
    // ------------------------------------------------------------------
    /** student_id => marks row. exam_marks overrides exam_subject_marks. */
    protected function markMap(int $examId, int $subjectId): Collection
    {
        $out = collect();
        foreach (['exam_subject_marks', 'exam_marks'] as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            foreach (DB::table($tbl)->where('exam_id', $examId)->where('subject_id', $subjectId)->get() as $m) {
                $out[$m->student_id] = $m;
            }
        }
        return $out;
    }

    /** One row per (exam, subject) the teacher must / may enter marks for. */
    protected function marksTasks(): Collection
    {
        $pairs = $this->pairs();
        if ($pairs->isEmpty()) return collect();

        $secIds = $pairs->pluck('section_id')->unique()->all();

        $exams = DB::table('exams as e')
            ->leftJoin('exam_types as et', 'et.id', '=', 'e.exam_type_id')
            ->whereIn('e.class_section_id', $secIds)
            ->orderByDesc('e.start_date')
            ->select('e.*', 'et.name as type_name')->get();
        if ($exams->isEmpty()) return collect();

        $examIds = $exams->pluck('id')->all();
        $sched = DB::table('exam_subject_schedules')->whereIn('exam_id', $examIds)->get()->groupBy('exam_id');

        $strength = DB::table('students')->whereIn('class_section_id', $secIds)->where('status', 'Active')
            ->selectRaw('class_section_id, COUNT(*) as c')->groupBy('class_section_id')->pluck('c', 'class_section_id');

        $entered = [];
        foreach (['exam_subject_marks', 'exam_marks'] as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            $rows = DB::table($tbl)->whereIn('exam_id', $examIds)->whereNotNull('marks_obtained')
                ->selectRaw('exam_id, subject_id, COUNT(*) as c')->groupBy('exam_id', 'subject_id')->get();
            foreach ($rows as $r) {
                $k = $r->exam_id . '|' . $r->subject_id;
                $entered[$k] = max($entered[$k] ?? 0, (int) $r->c);
            }
        }

        $tasks = collect();
        foreach ($exams as $e) {
            $s = $sched[$e->id] ?? collect();
            foreach ($pairs as $p) {
                if ($p->section_id !== (int) $e->class_section_id) continue;
                if ($s->isNotEmpty() && !$s->contains('subject_id', $p->subject_id)) continue;

                $row = $s->firstWhere('subject_id', $p->subject_id);
                $max = $row->max_marks ?? 100;
                $pass = $row->passing_marks ?? (int) round($max * ($e->passing_percentage ?? 40) / 100);

                $tasks->push((object) [
                    'key'     => $e->id . '|' . $p->subject_id,
                    'exam'    => $e,
                    'pair'    => $p,
                    'max'     => (int) $max,
                    'pass'    => (int) $pass,
                    'total'   => (int) ($strength[$p->section_id] ?? 0),
                    'entered' => (int) ($entered[$e->id . '|' . $p->subject_id] ?? 0),
                    'locked'  => (bool) $e->is_published,
                    'exam_date' => $row->exam_date ?? null,
                ]);
            }
        }
        return $tasks;
    }

    protected function gradeFor(?float $pct): string
    {
        if ($pct === null) return '—';
        if ($this->gradeScaleCache === null) {
            $json = DB::table('grade_scales')->where('is_default', 1)->value('grades')
                ?? DB::table('grade_scales')->value('grades');
            $this->gradeScaleCache = json_decode($json ?: '[]', true) ?: [];
        }
        foreach ($this->gradeScaleCache as $g) {
            if ($pct >= ($g['min'] ?? 0) && $pct <= ($g['max'] ?? 100) + 0.99) {
                return $g['grade'];
            }
        }
        return '—';
    }

    /** Insert or update with proper created_at/updated_at handling. */
    protected function upsert(string $table, array $keys, array $values): void
    {
        $q = DB::table($table)->where($keys);
        if ($q->exists()) {
            $q->update($values + ['updated_at' => now()]);
        } else {
            DB::table($table)->insert($keys + $values + ['created_at' => now(), 'updated_at' => now()]);
        }
    }

    /** All subject marks of a student for one exam (for report cards). */
    protected function studentSubjectMarks(int $examId, int $studentId): Collection
    {
        foreach (['exam_marks', 'exam_subject_marks'] as $table) {
            if (!Schema::hasTable($table)) continue;

            $rows = DB::table("$table as m")
                ->join('subjects as sub', 'sub.id', '=', 'm.subject_id')
                ->leftJoin('exam_subject_schedules as ess', function ($j) {
                    $j->on('ess.exam_id', '=', 'm.exam_id')->on('ess.subject_id', '=', 'm.subject_id');
                })
                ->where('m.exam_id', $examId)->where('m.student_id', $studentId)
                ->orderBy('sub.name')
                ->select('sub.name as subject', 'sub.code', 'm.marks_obtained', 'm.max_marks', 'm.passing_marks',
                         'm.remarks', 'ess.max_marks as sched_max', 'ess.passing_marks as sched_pass')
                ->get();

            if ($rows->isNotEmpty()) {
                return $rows->map(function ($r) {
                    $max  = $r->max_marks ?? $r->sched_max ?? 100;
                    $pass = $r->passing_marks ?? $r->sched_pass ?? 40;
                    $obt  = $r->marks_obtained;
                    return (object) [
                        'subject' => $r->subject, 'code' => $r->code, 'obtained' => $obt, 'max' => $max, 'passing' => $pass,
                        'pct'     => ($obt !== null && $max > 0) ? round($obt / $max * 100, 1) : null,
                        'passed'  => $obt !== null ? $obt >= $pass : null,
                        'remarks' => $r->remarks,
                    ];
                });
            }
        }
        return collect();
    }

    /** Counts of student attendance for a section+subject in a date range. */
    protected function attendanceCounts(int $sectionId, int $subjectId, ?string $from = null, ?string $to = null): Collection
    {
        return DB::table('student_attendance')
            ->where('class_section_id', $sectionId)->where('subject_id', $subjectId)
            ->when($from, fn ($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('date', '<=', $to))
            ->selectRaw('student_id, status, COUNT(*) as c')
            ->groupBy('student_id', 'status')->get()->groupBy('student_id');
    }

    protected function attendancePercent(?Collection $rows): array
    {
        $c = ['present' => 0, 'absent' => 0, 'late' => 0, 'half_day' => 0, 'leave' => 0, 'holiday' => 0, 'on_duty' => 0];
        foreach (($rows ?? collect()) as $r) {
            $c[$r->status] = ($c[$r->status] ?? 0) + (int) $r->c;
        }
        $total   = array_sum($c);
        $working = $total - $c['holiday'];
        $att     = $c['present'] + $c['late'] + $c['on_duty'] + 0.5 * $c['half_day'];
        return $c + ['total' => $total, 'pct' => $working > 0 ? round($att / $working * 100, 1) : 0];
    }
}
