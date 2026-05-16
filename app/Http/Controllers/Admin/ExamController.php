<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\ExamGroup;
use App\Models\Classes;
use App\Models\Student;
use App\Models\ExamMark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::with(['examType', 'examGroup', 'class'])
            ->orderBy('start_date', 'desc')
            ->paginate(20);
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $examTypes = ExamType::where('is_active', true)->orderBy('sort_order')->get();
        $examGroups = ExamGroup::where('is_active', true)->orderBy('sort_order')->get();
        $classes = Classes::join('grades', 'classes.grade_id', '=', 'grades.id')
            ->join('streams', 'classes.stream_id', '=', 'streams.id')
            ->orderBy('grades.name')
            ->orderBy('streams.name')
            ->orderBy('classes.section')
            ->get();
        return view('admin.exams.create', compact('examTypes', 'examGroups', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_type_id'  => 'required|exists:exam_types,id',
            'exam_group_id' => 'nullable|exists:exam_groups,id',
            'class_id'      => 'required|exists:classes,id',
            'name'          => 'required|string|max:255',
            'exam_center'   => 'nullable|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'time_table'    => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'description'   => 'nullable|string',
        ]);

        if ($request->hasFile('time_table')) {
            $validated['time_table'] = $request->file('time_table')->store('exam_timetables', 'public');
        }

        Exam::create($validated);
        return redirect()->route('admin.exams.index')->with('success', 'Exam created.');
    }

    public function edit(Exam $exam)
    {
        $examTypes = ExamType::where('is_active', true)->orderBy('sort_order')->get();
        $examGroups = ExamGroup::where('is_active', true)->orderBy('sort_order')->get();
        $classes = Classes::join('grades', 'classes.grade_id', '=', 'grades.id')
            ->join('streams', 'classes.stream_id', '=', 'streams.id')
            ->orderBy('grades.name')
            ->orderBy('streams.name')
            ->orderBy('classes.section')
            ->get();
        return view('admin.exams.edit', compact('exam', 'examTypes', 'examGroups', 'classes'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'exam_type_id'  => 'required|exists:exam_types,id',
            'exam_group_id' => 'nullable|exists:exam_groups,id',
            'class_id'      => 'required|exists:classes,id',
            'name'          => 'required|string|max:255',
            'exam_center'   => 'nullable|string|max:255',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'time_table'    => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'description'   => 'nullable|string',
        ]);

        if ($request->hasFile('time_table')) {
            if ($exam->time_table && Storage::disk('public')->exists($exam->time_table)) {
                Storage::disk('public')->delete($exam->time_table);
            }
            $validated['time_table'] = $request->file('time_table')->store('exam_timetables', 'public');
        }

        $exam->update($validated);
        return redirect()->route('admin.exams.index')->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        if ($exam->marks()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing marks.');
        }
        if ($exam->time_table && Storage::disk('public')->exists($exam->time_table)) {
            Storage::disk('public')->delete($exam->time_table);
        }
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted.');
    }

    public function publish(Exam $exam)
    {
        $exam->update(['is_published' => true]);
        return back()->with('success', 'Exam results published.');
    }

    public function unpublish(Exam $exam)
    {
        $exam->update(['is_published' => false]);
        return back()->with('success', 'Exam results hidden.');
    }

    // Marks entry form
    public function marksEntryForm(Exam $exam)
    {
        $students = $exam->students();
        $subjects = $exam->subjects();
        if ($students->isEmpty()) return back()->with('error', 'No students in this class.');
        if ($subjects->isEmpty()) return back()->with('error', 'No subjects assigned.');
        $existingMarks = ExamMark::where('exam_id', $exam->id)->get()->keyBy(fn($m) => $m->student_id . '_' . $m->subject_id);
        return view('admin.exams.marks-entry', compact('exam', 'students', 'subjects', 'existingMarks'));
    }

    public function storeMarks(Request $request, Exam $exam)
    {
        $request->validate([
            'marks' => 'required|array',
            'marks.*.*' => 'nullable|numeric|min:0',
            'max_marks' => 'required|array',
            'max_marks.*' => 'numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->marks as $studentId => $subjectMarks) {
                foreach ($subjectMarks as $subjectId => $marksObtained) {
                    if ($marksObtained === null || $marksObtained === '') continue;
                    $max = $request->max_marks[$subjectId] ?? 100;
                    $passing = $request->passing_marks[$subjectId] ?? ($max * 0.33);
                    ExamMark::updateOrCreate(
                        ['exam_id' => $exam->id, 'student_id' => $studentId, 'subject_id' => $subjectId],
                        ['marks_obtained' => $marksObtained, 'max_marks' => $max, 'passing_marks' => $passing]
                    );
                }
            }
            DB::commit();
            return redirect()->route('admin.exams.index')->with('success', 'Marks saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save marks: ' . $e->getMessage());
        }
    }
}