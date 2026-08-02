<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\SubjectAssignment;
use Illuminate\Http\Request;

class SubjectAssignmentController extends Controller
{
    /**
     * Display a listing of subject assignments
     */
    public function index(Request $request)
    {
        $query = SubjectAssignment::with(['classSection', 'subject', 'teacher']);

        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->class_section_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $assignments = $query->orderBy('class_section_id')->paginate(20);
        $classes = Classes::with(['grade', 'stream', 'sections'])->get();
        $classSections = ClassSection::with('class.grade', 'class.stream')->get();
        $subjects = Subject::all();

        return view('admin.subject-assignments.index', compact('assignments', 'classes', 'classSections', 'subjects'));
    }

    /**
     * Show the form for creating a new subject assignment
     */
    public function create(Request $request)
    {
        $classes = Classes::with(['grade', 'stream', 'sections'])->get();
        
        $selectedClass = null;
        $sections = collect();
        
        if ($request->has('class_id') && $request->class_id) {
            $selectedClass = Classes::with(['grade', 'stream', 'sections'])->find($request->class_id);
            if ($selectedClass) {
                $sections = $selectedClass->sections;
            }
        }
        
        $selectedSection = null;
        if ($request->has('class_section_id') && $request->class_section_id) {
            $selectedSection = ClassSection::with('class.grade', 'class.stream')->find($request->class_section_id);
        }
        
        $subjects = Subject::all();
        $teachers = Staff::where('is_teacher', true)->get();

        return view('admin.subject-assignments.create', compact(
            'classes', 'sections', 'selectedClass', 'selectedSection', 'subjects', 'teachers'
        ));
    }

    /**
     * Store a newly created subject assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:staff,id',
            'weekly_frequency' => 'nullable|integer|min:1|max:10',
            'is_elective' => 'boolean',
        ]);

        $exists = SubjectAssignment::where('class_section_id', $request->class_section_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This subject is already assigned to this section.')
                ->withInput();
        }

        SubjectAssignment::create([
            'class_section_id' => $request->class_section_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'weekly_frequency' => $request->weekly_frequency ?? 5,
            'is_elective' => $request->is_elective ?? false,
            'is_active' => true,
        ]);

        return redirect()->route('admin.subject-assignments.index')
            ->with('success', 'Subject assigned successfully.');
    }

    /**
     * Show the form for editing a subject assignment
     */
    public function edit($id)
    {
        $assignment = SubjectAssignment::with(['classSection', 'subject', 'teacher'])->findOrFail($id);
        $classes = Classes::with(['grade', 'stream', 'sections'])->get();
        $subjects = Subject::all();
        $teachers = Staff::where('is_teacher', true)->get();

        return view('admin.subject-assignments.edit', compact('assignment', 'classes', 'subjects', 'teachers'));
    }

    /**
     * Update a subject assignment
     */
    public function update(Request $request, $id)
    {
        $assignment = SubjectAssignment::findOrFail($id);

        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:staff,id',
            'weekly_frequency' => 'nullable|integer|min:1|max:10',
            'is_elective' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $exists = SubjectAssignment::where('class_section_id', $request->class_section_id)
            ->where('subject_id', $request->subject_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This subject is already assigned to this section.')
                ->withInput();
        }

        $assignment->update([
            'class_section_id' => $request->class_section_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'weekly_frequency' => $request->weekly_frequency,
            'is_elective' => $request->is_elective ?? false,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.subject-assignments.index')
            ->with('success', 'Subject assignment updated successfully.');
    }

    /**
     * Delete a subject assignment
     */
    public function destroy($id)
    {
        $assignment = SubjectAssignment::findOrFail($id);
        $assignment->delete();

        return redirect()->route('admin.subject-assignments.index')
            ->with('success', 'Subject assignment removed successfully.');
    }

    /**
     * Show bulk assignment form - FIXED: passes $classSections
     */
    public function bulkAssignForm()
    {
        $classes = Classes::with(['grade', 'stream', 'sections'])->get();
        $classSections = ClassSection::with(['class.grade', 'class.stream'])->get();
        $subjects = Subject::all();
        $teachers = Staff::where('is_teacher', true)->get();

        return view('admin.subject-assignments.bulk', compact('classes', 'classSections', 'subjects', 'teachers'));
    }

    /**
     * Process bulk assignment
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $classSectionId = $request->class_section_id;
        $subjectIds = $request->subject_ids;
        $teacherId = $request->teacher_id;
        $weeklyFrequency = $request->weekly_frequency ?? 5;

        $assigned = 0;
        $skipped = 0;

        foreach ($subjectIds as $subjectId) {
            $exists = SubjectAssignment::where('class_section_id', $classSectionId)
                ->where('subject_id', $subjectId)
                ->exists();

            if (!$exists) {
                SubjectAssignment::create([
                    'class_section_id' => $classSectionId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'weekly_frequency' => $weeklyFrequency,
                    'is_elective' => false,
                    'is_active' => true,
                ]);
                $assigned++;
            } else {
                $skipped++;
            }
        }

        $message = "Bulk assignment completed: {$assigned} subjects assigned, {$skipped} already existed.";
        return redirect()->route('admin.subject-assignments.index')
            ->with('success', $message);
    }

    /**
     * Get sections for a class (AJAX)
     */
    public function getSections($classId)
    {
        $sections = ClassSection::where('class_id', $classId)
            ->with('class.grade', 'class.stream')
            ->get()
            ->map(function($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->section_name,
                    'full_name' => $section->full_name,
                ];
            });
        
        return response()->json($sections);
    }

    /**
     * Get subjects for a section (AJAX)
     */
    public function getSubjectsBySection($sectionId)
    {
        try {
            $section = ClassSection::find($sectionId);
            if (!$section) {
                return response()->json([
                    'error' => 'Section not found',
                    'section_id' => $sectionId
                ], 404);
            }

            $assignments = SubjectAssignment::where('class_section_id', $sectionId)
                ->where('is_active', 1)
                ->with('subject')
                ->get();

            $subjects = [];
            foreach ($assignments as $assignment) {
                if ($assignment->subject) {
                    $subjects[] = [
                        'id' => $assignment->subject->id,
                        'name' => $assignment->subject->name,
                        'code' => $assignment->subject->code ?? 'N/A',
                        'type' => $assignment->subject->type ?? 'theory',
                        'assignment_id' => $assignment->id,
                        'weekly_frequency' => $assignment->weekly_frequency,
                        'is_elective' => $assignment->is_elective,
                    ];
                }
            }

            return response()->json($subjects);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assigned subjects with full details (AJAX)
     */
    public function getAssignedSubjects($sectionId)
    {
        $assignments = SubjectAssignment::where('class_section_id', $sectionId)
            ->with(['subject', 'teacher'])
            ->get();

        return response()->json($assignments);
    }

    /**
     * Get all subjects for a section (for dropdown)
     */
    public function getAvailableSubjects($sectionId)
    {
        $assignedSubjectIds = SubjectAssignment::where('class_section_id', $sectionId)
            ->pluck('subject_id')
            ->toArray();

        $subjects = Subject::where('is_active', true)
            ->whereNotIn('id', $assignedSubjectIds)
            ->get();

        return response()->json($subjects);
    }
}