<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarksController extends BaseTeacherController
{
    public function index()
    {
        $this->teacher();
        $tasks = $this->marksTasks();

        $open   = $tasks->where('locked', false)->values();
        $closed = $tasks->where('locked', true)->values();

        return view('teacher.marks.index', compact('open', 'closed'));
    }

    public function create(Request $request)
    {
        $this->teacher();
        $tasks = $this->marksTasks();

        $task = null; $students = collect(); $marks = collect();

        if ($request->filled('task')) {
            $task = $tasks->firstWhere('key', $request->task);
            abort_if(!$task, 403, 'You cannot enter marks for this exam/subject.');
            $students = $this->studentsOfSection($task->pair->section_id);
            $marks = $this->markMap((int) $task->exam->id, (int) $task->pair->subject_id);
        }

        return view('teacher.marks.create', compact('tasks', 'task', 'students', 'marks'));
    }

    public function store(Request $request)
    {
        $this->teacher();

        $request->validate([
            'task'        => 'required|string',
            'marks'       => 'nullable|array',
            'marks.*'     => 'nullable|integer|min:0',
            'remarks'     => 'nullable|array',
            'remarks.*'   => 'nullable|string|max:255',
        ], [
            'marks.*.integer' => 'Marks must be whole numbers.',
            'marks.*.min'     => 'Marks cannot be negative.',
        ]);

        $task = $this->marksTasks()->firstWhere('key', $request->task);
        abort_if(!$task, 403, 'You cannot enter marks for this exam/subject.');

        if ($task->locked) {
            return back()->with('error', 'This exam is published. Marks are locked — contact the admin to make changes.');
        }

        $students = $this->studentsOfSection($task->pair->section_id);
        $marks = $request->input('marks', []);
        $remarks = $request->input('remarks', []);

        $errors = [];
        foreach ($students as $s) {
            $v = $marks[$s->id] ?? null;
            if ($v !== null && $v !== '' && (int) $v > $task->max) {
                $errors[] = trim($s->first_name . ' ' . $s->last_name) . ": {$v} exceeds maximum {$task->max}.";
            }
        }
        if ($errors) {
            return back()->withInput()->withErrors($errors);
        }

        $tables = array_values(array_filter(['exam_marks', 'exam_subject_marks'], fn ($t) => Schema::hasTable($t)));

        DB::transaction(function () use ($students, $marks, $remarks, $task, $tables) {
            foreach ($students as $s) {
                $v = $marks[$s->id] ?? null;
                $v = ($v === null || $v === '') ? null : (int) $v;

                foreach ($tables as $tbl) {
                    $this->upsert($tbl,
                        ['exam_id' => $task->exam->id, 'student_id' => $s->id, 'subject_id' => $task->pair->subject_id],
                        [
                            'marks_obtained' => $v,
                            'max_marks'      => $task->max,
                            'passing_marks'  => $task->pass,
                            'remarks'        => $remarks[$s->id] ?? null,
                        ]);
                }
            }
        });

        return redirect()->route('teacher.marks.create', ['task' => $request->task])
            ->with('success', 'Marks saved successfully.');
    }
}
