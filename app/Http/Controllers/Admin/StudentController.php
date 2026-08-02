<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\AcademicSession;
use App\Models\Grade;
use App\Models\Stream;
use App\Models\ExamMark;
use App\Models\ExamResult;
use App\Models\StudentAttendance;
use App\Models\StudentFeeSubmission; // ← CHANGED
use App\Models\Invoice;
use App\Models\StudentDiscount;
use App\Models\Discount;
use App\Models\CertificateDistribution;
use App\Models\StudentTransfer;
use App\Models\TransferCertificate;
use App\Models\TimetableEntry;
use App\Models\TimeSlot;
use App\Models\FeeSubmissionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['classSection.class.grade']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('class_section_id')) {
            $query->where('class_section_id', $request->class_section_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->orderBy('first_name')->paginate(20);
        $classSections = ClassSection::with('class.grade')->get();

        return view('admin.students.index', compact('students', 'classSections'));
    }

    public function create()
{
    // Get all classes for the dropdown (this is what your view expects)
    $classes = \App\Models\Classes::with(['grade', 'stream'])->get();
    
    // Get class sections for other dropdowns
    $classSections = ClassSection::with('class.grade')->get();
    
    // Get grades, streams, academic sessions
    $grades = Grade::all();
    $streams = Stream::all();
    $academicSessions = AcademicSession::where('is_active', true)->get();
    $feeTypes = FeeSubmissionType::where('is_active', true)->get();

    return view('admin.students.create', compact(
        'classes',        // ← ADD THIS
        'classSections', 
        'grades', 
        'streams', 
        'academicSessions',
        'feeTypes'
    ));
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'admission_date' => 'required|date',
            'admission_number' => 'required|unique:students',
            'class_section_id' => 'required|exists:class_sections,id',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('students/photos', 'public');
                $data['profile_photo'] = $path;
            }

            $data['dob_in_words'] = Carbon::parse($request->date_of_birth)->format('F d, Y');
            $data['student_type'] = $request->student_type ?? 'New';
            $data['status'] = 'Active';

            $student = Student::create($data);
            $this->createDefaultFeeSubmissions($student); // ← CHANGED

            DB::commit();

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create student: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $student = Student::with([
            'classSection.class.grade',
            'classSection.class.stream',
            'attendances' => function($q) {
                $q->orderBy('date', 'desc')->limit(30);
            },
            'examMarks.subject',
            'examResults' => function($q) {
                $q->orderBy('created_at', 'desc');
            },
            'feeSubmissions.feeSubmissionType', // ← CHANGED
            'invoices',
            'discounts',
            'certificates',
            'transfers'
        ])->findOrFail($id);

        $stats = $this->calculateStudentStats($student);
        $recentAttendance = $student->attendances()->latest()->take(10)->get();
        $examPerformance = $this->getExamPerformance($student);
        $feeSummary = $this->getFeeSummary($student);

        // Get timetable for student's class
        $timetable = TimetableEntry::with(['subject', 'teacher', 'room', 'timeSlot', 'classSection'])
            ->where('class_section_id', $student->class_section_id)
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get()
            ->groupBy('day_of_week');

        $timeSlots = TimeSlot::where('is_active', true)
            ->where('type', 'period')
            ->orderBy('sort_order')
            ->get();

        return view('admin.students.show', compact(
            'student', 
            'stats', 
            'recentAttendance', 
            'examPerformance', 
            'feeSummary',
            'timetable',
            'timeSlots'
        ));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classSections = ClassSection::with('class.grade')->get();
        $grades = Grade::all();
        $streams = Stream::all();
        $academicSessions = AcademicSession::where('is_active', true)->get();

        return view('admin.students.edit', compact('student', 'classSections', 'grades', 'streams', 'academicSessions'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'admission_date' => 'required|date',
            'admission_number' => 'required|unique:students,admission_number,' . $id,
            'class_section_id' => 'required|exists:class_sections,id',
            'father_name' => 'required|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->all();
            
            if ($request->hasFile('profile_photo')) {
                if ($student->profile_photo) {
                    Storage::disk('public')->delete($student->profile_photo);
                }
                $path = $request->file('profile_photo')->store('students/photos', 'public');
                $data['profile_photo'] = $path;
            }

            $data['dob_in_words'] = Carbon::parse($request->date_of_birth)->format('F d, Y');
            $student->update($data);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} updated successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $name = $student->full_name;
            
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            
            $student->delete();

            return redirect()->route('admin.students.index')
                ->with('success', "Student {$name} deleted successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function getStudentsByClassSection($classSectionId)
    {
        $students = Student::where('class_section_id', $classSectionId)
                          ->where('status', 'Active')
                          ->orderBy('first_name')
                          ->get(['id', 'first_name', 'last_name', 'admission_number', 'roll_number']);

        return response()->json($students);
    }

    public function promote(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $newClassSectionId = $request->class_section_id;

        if (!$newClassSectionId) {
            return redirect()->back()->with('error', 'Please select a new class section.');
        }

        try {
            $student->update(['class_section_id' => $newClassSectionId]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} promoted successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to promote student: ' . $e->getMessage());
        }
    }

    public function suspend(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'suspension_start_date' => 'required|date',
            'suspension_end_date' => 'required|date|after:suspension_start_date',
            'suspension_message' => 'required|string|max:500',
        ]);

        try {
            $student->update([
                'status' => 'Inactive',
                'suspension_start_date' => $request->suspension_start_date,
                'suspension_end_date' => $request->suspension_end_date,
                'suspension_message' => $request->suspension_message,
            ]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} suspended successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to suspend student: ' . $e->getMessage());
        }
    }

    public function reactivate($id)
    {
        $student = Student::findOrFail($id);

        try {
            $student->update([
                'status' => 'Active',
                'suspension_start_date' => null,
                'suspension_end_date' => null,
                'suspension_message' => null,
            ]);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', "Student {$student->full_name} reactivated successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reactivate student: ' . $e->getMessage());
        }
    }

    /**
 * Show student attendance report
 */
public function attendanceReport($id)
{
    $student = Student::with(['classSection'])->findOrFail($id);
    
    $attendances = StudentAttendance::where('student_id', $id)
        ->orderBy('date', 'desc')
        ->paginate(30);

    // Calculate summary
    $total = $attendances->total();
    $present = StudentAttendance::where('student_id', $id)->where('status', 'present')->count();
    $absent = StudentAttendance::where('student_id', $id)->where('status', 'absent')->count();
    $late = StudentAttendance::where('student_id', $id)->where('status', 'late')->count();
    $halfDay = StudentAttendance::where('student_id', $id)->where('status', 'half_day')->count();
    $leave = StudentAttendance::where('student_id', $id)->where('status', 'leave')->count();

    $attended = $present + $late + $halfDay;
    $percentage = $total > 0 ? round(($attended / $total) * 100, 2) : 0;

    $summary = [
        'total' => $total,
        'present' => $present,
        'absent' => $absent,
        'late' => $late,
        'half_day' => $halfDay,
        'leave' => $leave,
        'percentage' => $percentage,
    ];

    return view('admin.students.attendance', compact('student', 'attendances', 'summary'));
}

   /**
 * Show student exam results
 */
public function examResults($id)
{
    $student = Student::with(['classSection'])->findOrFail($id);
    
    $examResults = ExamResult::with(['exam', 'exam.examType'])
        ->where('student_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    $subjectWise = ExamMark::with(['subject', 'exam'])
        ->where('student_id', $id)
        ->orderBy('subject_id')
        ->get()
        ->groupBy('subject_id');

    return view('admin.students.exam-results', compact('student', 'examResults', 'subjectWise'));
}

    /**
 * Show student fee details
 */
public function feeDetails($id)
{
    $student = Student::findOrFail($id);
    
    // Use feeSubmissions instead of feeInstallments
    $feeSubmissions = StudentFeeSubmission::with(['feeSubmissionType'])
        ->where('student_id', $id)
        ->orderBy('due_date')
        ->get();

    $invoices = Invoice::with(['bank'])
        ->where('student_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    $totalPaid = $feeSubmissions->where('status', 'paid')->sum('paid_amount');
    $totalDue = $feeSubmissions->whereIn('status', ['pending', 'partial'])->sum('amount');
    $totalDiscount = 0;

    return view('admin.students.fee-details', compact(
        'student', 
        'feeSubmissions', 
        'invoices', 
        'totalPaid', 
        'totalDue', 
        'totalDiscount'
    ));
}

    // ← CHANGED: Method name and implementation
    private function createDefaultFeeSubmissions($student)
    {
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();
        
        foreach ($feeTypes as $feeType) {
            StudentFeeSubmission::create([
                'student_id' => $student->id,
                'fee_submission_type_id' => $feeType->id,
                'installment_number' => 1,
                'amount' => $feeType->amount,
                'due_date' => now()->addMonths(1),
                'status' => 'pending',
                'paid_amount' => 0,
            ]);
        }
    }

    // ← CHANGED: Use feeSubmissions instead of feeInstallments
    private function calculateStudentStats($student)
    {
        $totalDays = $student->attendances()->count();
        $presentDays = $student->attendances()->where('status', 'present')->count();
        $absentDays = $student->attendances()->where('status', 'absent')->count();
        $lateDays = $student->attendances()->where('status', 'late')->count();
        $halfDayDays = $student->attendances()->where('status', 'half_day')->count();
        $leaveDays = $student->attendances()->where('status', 'leave')->count();
        
        $attended = $presentDays + $lateDays + $halfDayDays;
        $attendancePercentage = $totalDays > 0 ? round(($attended / $totalDays) * 100, 2) : 0;

        $totalExams = $student->examResults()->count();
        $avgPercentage = $student->examResults()->avg('percentage') ?? 0;
        $grade = $this->calculateGrade($avgPercentage);

        // ← CHANGED: Use feeSubmissions instead of feeInstallments
        $feeSubmissions = $student->feeSubmissions;
        $totalFee = $feeSubmissions->sum('amount');
        $paidFee = $feeSubmissions->where('status', 'paid')->sum('paid_amount');
        $pendingFee = $totalFee - $paidFee;

        return [
            'total_attendance_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'half_day_days' => $halfDayDays,
            'leave_days' => $leaveDays,
            'attendance_percentage' => $attendancePercentage,
            'total_exams' => $totalExams,
            'avg_percentage' => $avgPercentage,
            'grade' => $grade,
            'total_fee' => $totalFee,
            'paid_fee' => $paidFee,
            'pending_fee' => $pendingFee,
        ];
    }

    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    private function getExamPerformance($student)
    {
        return ExamResult::with(['exam'])
                         ->where('student_id', $student->id)
                         ->orderBy('created_at', 'desc')
                         ->take(5)
                         ->get();
    }

    // ← CHANGED: Use feeSubmissions instead of feeInstallments
    private function getFeeSummary($student)
    {
        $feeSubmissions = $student->feeSubmissions;
        return [
            'total' => $feeSubmissions->sum('amount'),
            'paid' => $feeSubmissions->where('status', 'paid')->sum('paid_amount'),
            'pending' => $feeSubmissions->whereIn('status', ['pending', 'partial'])->sum('amount'),
            'overdue' => $feeSubmissions->where('status', 'pending')->where('due_date', '<', now())->count(),
            'total_installments' => $feeSubmissions->count(),
            'paid_installments' => $feeSubmissions->where('status', 'paid')->count(),
            'pending_installments' => $feeSubmissions->where('status', 'pending')->count(),
            'partial_installments' => $feeSubmissions->where('status', 'partial')->count(),
        ];
    }

    
}