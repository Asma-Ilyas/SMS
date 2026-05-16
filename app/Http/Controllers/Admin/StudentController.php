<?php
// app/Http/Controllers/Admin/StudentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\Classes;
use App\Models\StudentFeeInstallment;
use App\Models\AcademicSession;
use App\Models\FeeSubmissionType;
use App\Models\Grade;
use App\Models\Stream;
use App\Models\ElectiveTrack;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('class')->latest()->paginate(20);
    $allClasses = Classes::with(['grade', 'stream', 'electiveTrack'])->get();
    $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
    return view('admin.students.index', compact('students', 'allClasses', 'sessions'));
    }

    public function create()
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->get();
    $grades = Grade::orderBy('numeric_value')->get();
    $streams = Stream::all();
    $electiveTracks = ElectiveTrack::with('stream')->get();
    $feeTypes = FeeSubmissionType::where('is_active', true)->orderBy('name')->get();
    
    // Add this line to fetch all classes with their relationships
    $classes = Classes::with(['grade', 'stream', 'electiveTrack'])->get();
    
    return view('admin.students.create', compact('sessions', 'grades', 'streams', 'electiveTracks', 'feeTypes', 'classes'));
    }

    

public function store(StoreStudentRequest $request)
{
    $data = $request->validated();
    
    // ... handle file uploads (profile_photo, parent_id_proof, parent_signature) as before ...
    
    $student = Student::create($data);
    
    // Process fees if any
    if ($request->has('fee_items') && is_array($request->fee_items)) {
        foreach ($request->fee_items as $item) {
            if (empty($item['fee_submission_type_id'])) continue;
            
            $feeType = FeeSubmissionType::find($item['fee_submission_type_id']);
            if (!$feeType) continue;
            
            $period = $item['period'] ?? $feeType->period;
            $amount = !empty($item['amount']) ? $item['amount'] : $feeType->amount;
            $installmentCount = isset($item['installment_count']) ? (int)$item['installment_count'] : 1;
            
            if ($installmentCount == 1) {
                // Single payment (one-time or no installments)
                StudentFeeInstallment::create([
                    'student_id' => $student->id,
                    'fee_submission_type_id' => $feeType->id,
                    'installment_number' => 1,
                    'amount' => $amount,
                    'due_date' => now()->addDays(30), // default due 30 days from admission
                    'status' => 'pending',
                    'paid_amount' => 0,
                ]);
            } else {
                // Split into installments
                $perInstallment = round($amount / $installmentCount, 2);
                $lastAdjustment = $amount - ($perInstallment * ($installmentCount - 1));
                
                for ($i = 1; $i <= $installmentCount; $i++) {
                    $installmentAmount = ($i == $installmentCount) ? $lastAdjustment : $perInstallment;
                    StudentFeeInstallment::create([
                        'student_id' => $student->id,
                        'fee_submission_type_id' => $feeType->id,
                        'installment_number' => $i,
                        'amount' => $installmentAmount,
                        'due_date' => now()->addMonths($i * 1), // monthly installments – adjust as needed
                        'status' => 'pending',
                        'paid_amount' => 0,
                    ]);
                }
            }
        }
    }
    
    return redirect()->route('admin.students.index')
        ->with('success', 'Student admitted successfully with fee structure.');
}
    

    public function show(Student $student)
    {
        $student->load('class');
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = Classes::with(['grade', 'stream', 'electiveTrack'])->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) Storage::disk('public')->delete($student->profile_photo);
            $data['profile_photo'] = $request->file('profile_photo')->store('students/photos', 'public');
        }

        if ($request->hasFile('parent_id_proof')) {
            if ($student->parent_id_proof) Storage::disk('public')->delete($student->parent_id_proof);
            $data['parent_id_proof'] = $request->file('parent_id_proof')->store('students/id_proofs', 'public');
        }

        if ($request->hasFile('parent_signature')) {
            if ($student->parent_signature) Storage::disk('public')->delete($student->parent_signature);
            $data['parent_signature'] = $request->file('parent_signature')->store('students/signatures', 'public');
        }

        if (!$request->filled('suspension_start_date')) {
            $data['suspension_start_date'] = null;
            $data['suspension_end_date'] = null;
            $data['suspension_message'] = null;
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->profile_photo) Storage::disk('public')->delete($student->profile_photo);
        if ($student->parent_id_proof) Storage::disk('public')->delete($student->parent_id_proof);
        if ($student->parent_signature) Storage::disk('public')->delete($student->parent_signature);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
 

// Promote individual student
public function promote(Request $request, Student $student)
{
    $request->validate([
        'target_class_id' => 'required|exists:classes,id',
        'target_academic_session_id' => 'nullable|exists:academic_sessions,id',
    ]);

    $oldClass = $student->class;
    $student->class_id = $request->target_class_id;
    if ($request->target_academic_session_id) {
        $student->academic_session_id = $request->target_academic_session_id;
    }
    $student->save();

    // Optionally log promotion
    // You can also create a history table if needed

    return redirect()->route('admin.students.index')
        ->with('success', "Student {$student->full_name} promoted from {$oldClass->full_name} to new class.");
}

// Promote whole class
public function promoteClass(Request $request, Classes $class)
{
    $request->validate([
        'target_class_id' => 'required|exists:classes,id',
        'target_academic_session_id' => 'nullable|exists:academic_sessions,id',
    ]);

    $students = Student::where('class_id', $class->id)->get();
    $count = 0;
    foreach ($students as $student) {
        $student->class_id = $request->target_class_id;
        if ($request->target_academic_session_id) {
            $student->academic_session_id = $request->target_academic_session_id;
        }
        $student->save();
        $count++;
    }

    return redirect()->route('admin.students.index')
        ->with('success', "$count students promoted from class {$class->full_name}.");
}

public function getSectionsByClass($class_id)
{
    $sections = Student::where('class_id', $class_id)
                ->distinct()
                ->pluck('section');
    return response()->json($sections);
}

public function getStudentsByClassSection($class_id, $section)
{
    $students = Student::where('class_id', $class_id)
                ->where('section', $section)
                ->get(['id', 'first_name', 'middle_name', 'last_name', 'admission_number']);
    return response()->json($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->full_name . ' (' . $s->admission_number . ')'
    ]));
}
}