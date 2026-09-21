<?php

namespace App\Http\Controllers\Teacher;

use App\Support\PortalAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Student attendance marked by the teacher. */
class AttendanceController extends BaseTeacherController
{
    public function index()
    {
        $t = $this->teacher();
        $today = Carbon::today();
        $pairs = $this->pairs(true);

        $marked = collect();
        $strength = collect();
        if ($pairs->isNotEmpty()) {
            $secIds = $pairs->pluck('section_id')->unique()->all();
            $marked = DB::table('student_attendance')->whereDate('date', $today)->whereIn('class_section_id', $secIds)
                ->selectRaw('class_section_id, subject_id, COUNT(*) as c')->groupBy('class_section_id', 'subject_id')->get()
                ->keyBy(fn ($r) => $r->class_section_id . '|' . $r->subject_id);
            $strength = DB::table('students')->whereIn('class_section_id', $secIds)->where('status', 'Active')
                ->selectRaw('class_section_id, COUNT(*) as c')->groupBy('class_section_id')->pluck('c', 'class_section_id');
        }

        $rows = $pairs->map(function ($p) use ($marked, $strength) {
            $p->total  = (int) ($strength[$p->section_id] ?? 0);
            $p->marked = (int) ($marked[$p->key]->c ?? 0);
            return $p;
        });

        $recent = DB::table('student_attendance as a')
            ->join('class_sections as cs', 'cs.id', '=', 'a.class_section_id')
            ->join('classes as c', 'c.id', '=', 'cs.class_id')
            ->join('grades as g', 'g.id', '=', 'c.grade_id')
            ->join('subjects as sub', 'sub.id', '=', 'a.subject_id')
            ->where('a.teacher_id', $t->id)
            ->groupBy('a.date', 'a.class_section_id', 'a.subject_id', 'g.name', 'cs.section_name', 'sub.name')
            ->orderByDesc('a.date')->limit(15)
            ->selectRaw("a.date, a.class_section_id, a.subject_id, g.name as grade_name, cs.section_name, sub.name as subject_name,
                SUM(a.status = 'present') as present, SUM(a.status = 'absent') as absent, SUM(a.status = 'late') as late, COUNT(*) as total")
            ->get();

        return view('teacher.attendance.index', compact('rows', 'recent', 'today'));
    }

    public function create(Request $request)
    {
        $this->teacher();
        $pairs = $this->pairs(true);

        try {
            $date = Carbon::parse($request->get('date', today()->toDateString()))->startOfDay();
        } catch (\Throwable $e) {
            $date = Carbon::today();
        }
        if ($date->gt(Carbon::today())) {
            $date = Carbon::today();
        }

        $sel = null;
        $students = collect();
        $existing = collect();

        if ($request->filled('pair')) {
            [$sec, $sub] = array_map('intval', explode('|', $request->pair) + [0, 0]);
            $sel = $this->findPair($sec, $sub, true);
            $students = $this->studentsOfSection($sec);
            $existing = DB::table('student_attendance')
                ->where('class_section_id', $sec)->where('subject_id', $sub)->whereDate('date', $date)
                ->get()->keyBy('student_id');
        }

        return view('teacher.attendance.create', compact('pairs', 'sel', 'students', 'existing', 'date'));
    }

    public function store(Request $request)
    {
        $t = $this->teacher();

        $request->validate([
            'pair'                          => 'required|string',
            'date'                          => 'required|date|before_or_equal:today',
            'attendance'                    => 'required|array|min:1',
            'attendance.*.status'           => 'required|in:present,absent,late,half_day,leave,on_duty',
            'attendance.*.remarks'          => 'nullable|string|max:255',
            'attendance.*.late_minutes'     => 'nullable|integer|min:0|max:600',
            'attendance.*.half_day_type'    => 'nullable|in:morning,afternoon,custom',
        ]);

        [$sec, $sub] = array_map('intval', explode('|', $request->pair) + [0, 0]);
        $this->findPair($sec, $sub, true);

        $date = Carbon::parse($request->date)->toDateString();
        $validIds = $this->studentsOfSection($sec)->pluck('id')->map(fn ($i) => (int) $i)->all();

        $canApprove = Schema::hasTable('attendance_teacher_section')
            && DB::table('attendance_teacher_section')->where('teacher_id', $t->id)
                ->where('class_section_id', $sec)->where('can_approve_leave', 1)->exists();

        $saved = 0;
        $newLeaves = 0; // leaves that now wait for an admin's approval

        DB::transaction(function () use ($request, $t, $sec, $sub, $date, $validIds, $canApprove, &$saved, &$newLeaves) {
            foreach ($request->attendance as $sid => $row) {
                $sid = (int) $sid;
                if (!in_array($sid, $validIds, true)) continue;

                $status  = $row['status'];
                $remarks = $row['remarks'] ?? null;
                $isLeave = $status === 'leave';

                // Count only leaves that were not already leaves, so re-saving the
                // same page does not notify the admins a second time.
                if ($isLeave && !$canApprove) {
                    $before = DB::table('student_attendance')
                        ->where('student_id', $sid)->where('date', $date)->where('subject_id', $sub)
                        ->value('status');
                    if ($before !== 'leave') {
                        $newLeaves++;
                    }
                }

                $this->upsert('student_attendance',
                    ['student_id' => $sid, 'date' => $date, 'subject_id' => $sub],
                    [
                        'class_section_id' => $sec,
                        'teacher_id'       => $t->id,
                        'status'           => $status,
                        'half_day_type'    => $status === 'half_day' ? ($row['half_day_type'] ?? 'morning') : null,
                        'half_day_reason'  => $status === 'half_day' ? $remarks : null,
                        'late_minutes'     => $status === 'late' ? (int) ($row['late_minutes'] ?? 0) : 0,
                        'late_reason'      => $status === 'late' ? $remarks : null,
                        'leave_reason'     => $isLeave ? $remarks : null,
                        'remarks'          => $remarks,
                        'is_approved'      => !$isLeave || $canApprove,
                        'approved_by'      => ($isLeave && $canApprove) ? $t->id : null,
                        'approved_at'      => ($isLeave && $canApprove) ? now() : null,
                        'marked_by'        => $t->id,
                    ]);
                $saved++;
            }
        });

        // Tell the admins there is something to approve (never blocks saving).
        if ($newLeaves > 0) {
            PortalAlert::toAdmins(
                'Student leave needs approval',
                $newLeaves . ' student leave ' . ($newLeaves === 1 ? 'request was' : 'requests were')
                    . ' marked by ' . trim($t->first_name . ' ' . $t->last_name) . ' and waiting for approval.',
                route('admin.studentattendance.pending-leaves'),
                'attendance',
                'info'
            );
        }

        return redirect()->route('teacher.attendance.create', ['pair' => $request->pair, 'date' => $date])
            ->with('success', "Attendance saved for {$saved} student(s).");
    }

    public function report(Request $request)
    {
        $this->teacher();
        $pairs = $this->pairs(true);

        try {
            $month = Carbon::createFromFormat('Y-m', $request->get('month', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }

        $sel = null; $students = collect(); $dates = collect(); $matrix = []; $summary = [];

        if ($request->filled('pair')) {
            [$sec, $sub] = array_map('intval', explode('|', $request->pair) + [0, 0]);
            $sel = $this->findPair($sec, $sub, true);
            $students = $this->studentsOfSection($sec);

            $rows = DB::table('student_attendance')
                ->where('class_section_id', $sec)->where('subject_id', $sub)
                ->whereBetween('date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
                ->get();

            $dates = $rows->pluck('date')->map(fn ($d) => substr((string) $d, 0, 10))->unique()->sort()->values();

            foreach ($rows->groupBy('student_id') as $sid => $list) {
                foreach ($list as $r) {
                    $matrix[$sid][substr((string) $r->date, 0, 10)] = $r->status;
                }
                $summary[$sid] = $this->attendancePercent(
                    $list->groupBy('status')->map(fn ($g, $st) => (object) ['status' => $st, 'c' => $g->count()])->values()
                );
            }

            if ($request->get('export') === 'csv') {
                return $this->csv($sel, $students, $dates, $matrix, $summary, $month);
            }
        }

        return view('teacher.attendance.report', compact('pairs', 'sel', 'students', 'dates', 'matrix', 'summary', 'month'));
    }

    private function csv($sel, $students, $dates, $matrix, $summary, $month)
    {
        $codes = ['present' => 'P', 'absent' => 'A', 'late' => 'L', 'half_day' => 'H', 'leave' => 'V', 'on_duty' => 'D', 'holiday' => 'O'];
        $name = 'attendance-' . $month->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($sel, $students, $dates, $matrix, $summary, $codes) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [$sel->label]);
            fputcsv($out, array_merge(['Roll', 'Admission #', 'Student'], $dates->map(fn ($d) => substr($d, 8, 2))->all(), ['Present', 'Absent', 'Late', '%']));
            foreach ($students as $s) {
                $sm = $summary[$s->id] ?? null;
                $line = [$s->roll_number, $s->admission_number, trim($s->first_name . ' ' . $s->last_name)];
                foreach ($dates as $d) $line[] = $codes[$matrix[$s->id][$d] ?? ''] ?? '';
                $line[] = $sm['present'] ?? 0; $line[] = $sm['absent'] ?? 0; $line[] = $sm['late'] ?? 0; $line[] = $sm['pct'] ?? 0;
                fputcsv($out, $line);
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv']);
    }
}