<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use App\Models\StudentFeeInstallment;
use App\Models\TransferCertificate;
use Illuminate\Http\Request;

class TransferCertificateController extends Controller
{
    public function index()
    {
        $certificates = TransferCertificate::with('student')->latest()->paginate(20);
        $classes = Classes::with(['grade', 'stream'])->get();
        return view('admin.transfer-certificates.index', compact('certificates', 'classes'));
    }

    // AJAX: Get sections by class
    public function getSections($classId)
    {
        $sections = Student::where('class_id', $classId)->distinct()->pluck('section');
        return response()->json($sections);
    }

    // AJAX: Get students by class & section
    public function getStudents($classId, $section)
    {
        $students = Student::where('class_id', $classId)
            ->where('section', $section)
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'admission_number']);
        return response()->json($students);
    }

    // Get student full data for preview
    public function getStudentData($studentId)
    {
        $student = Student::with(['class.grade', 'class.stream'])->findOrFail($studentId);

        // Fee summary
        $fees = StudentFeeInstallment::where('student_id', $studentId)->get();
        $totalAmount = $fees->sum('amount');
        $totalPaid = $fees->sum('paid_amount');
        $totalDue = $totalAmount - $totalPaid;

        // Exam records (placeholder – adjust to your exam module)
        $exams = []; // You can replace with actual exam results

        return response()->json([
            'student' => $student,
            'fee_summary' => [
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
            ],
            'exams' => $exams,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'certificate_types' => 'required|array|min:1',
            'student_status_after' => 'required|in:active,inactive',
            'remarks' => 'nullable|string',
        ]);

        $certificate = TransferCertificate::create([
            'student_id' => $request->student_id,
            'certificate_number' => TransferCertificate::generateCertificateNumber(),
            'certificate_types' => $request->certificate_types,
            'student_status_after' => $request->student_status_after,
            'remarks' => $request->remarks,
            'issued_date' => now(),
        ]);

        // Update student status if set to inactive
        if ($request->student_status_after == 'inactive') {
            $student = Student::find($request->student_id);
            $student->status = 'Inactive';
            $student->save();
        }

        return redirect()->route('admin.transfer-certificates.index')
            ->with('success', 'Transfer certificate(s) issued successfully.');
    }
}