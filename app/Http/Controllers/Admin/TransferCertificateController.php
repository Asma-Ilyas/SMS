<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\TransferCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferCertificateController extends Controller
{
    /**
     * Display a listing of transfer certificates.
     */
    public function index(Request $request)
    {
        $query = TransferCertificate::with(['student', 'student.classSection']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', '%' . $search . '%')
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('admission_number', 'like', '%' . $search . '%');
                  });
            });
        }
        
        if ($request->filled('class_id')) {
            $query->whereHas('student.classSection', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        if ($request->filled('section_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_section_id', $request->section_id);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('student_status_after', $request->status);
        }
        
        $certificates = $query->orderBy('issued_date', 'desc')->paginate(20);
        
        // Get all classes for filter dropdown
        $classes = Classes::with(['grade', 'stream'])->get();
        
        // Get all sections for filter dropdown
        $sections = ClassSection::with(['class.grade', 'class.stream'])->get();
        
        // Get stats
        $totalCertificates = TransferCertificate::count();
        $activeStudents = TransferCertificate::where('student_status_after', 'active')->count();
        $inactiveStudents = TransferCertificate::where('student_status_after', 'inactive')->count();
        
        return view('admin.transfer-certificates.index', compact(
            'certificates', 
            'classes', 
            'sections', 
            'totalCertificates',
            'activeStudents',
            'inactiveStudents'
        ));
    }

    /**
     * Show the form for creating a new transfer certificate.
     */
    public function create()
    {
        $students = Student::with(['classSection', 'classSection.class.grade'])
            ->where('status', 'Active')
            ->orderBy('first_name')
            ->get();
            
        $classes = Classes::with(['grade', 'stream'])->get();
        $sections = ClassSection::with(['class.grade', 'class.stream'])->get();
            
        return view('admin.transfer-certificates.create', compact('students', 'classes', 'sections'));
    }

    /**
     * Store a newly created transfer certificate in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'certificate_types' => 'required|array',
            'certificate_types.*' => 'string',
            'student_status_after' => 'required|in:active,inactive',
            'remarks' => 'nullable|string',
            'issued_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Generate certificate number
            $certificateNumber = 'TC-' . date('Y') . '-' . str_pad(TransferCertificate::count() + 1, 5, '0', STR_PAD_LEFT);
            
            // Check for duplicate
            while (TransferCertificate::where('certificate_number', $certificateNumber)->exists()) {
                $certificateNumber = 'TC-' . date('Y') . '-' . str_pad(TransferCertificate::count() + 2, 5, '0', STR_PAD_LEFT);
            }

            $certificate = TransferCertificate::create([
                'student_id' => $request->student_id,
                'certificate_number' => $certificateNumber,
                'certificate_types' => json_encode($request->certificate_types),
                'student_status_after' => $request->student_status_after,
                'remarks' => $request->remarks,
                'issued_date' => $request->issued_date,
            ]);

            // Update student status if needed
            if ($request->student_status_after === 'inactive') {
                $student = Student::find($request->student_id);
                if ($student) {
                    $student->update(['status' => 'Inactive']);
                }
            }

            DB::commit();

            return redirect()->route('admin.transfer-certificates.index')
                ->with('success', 'Transfer certificate created successfully. Number: ' . $certificateNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create transfer certificate: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified transfer certificate.
     */
    public function show(TransferCertificate $transferCertificate)
    {
        $transferCertificate->load(['student', 'student.classSection', 'student.classSection.class.grade']);
        return view('admin.transfer-certificates.show', compact('transferCertificate'));
    }

    /**
     * Show the form for editing the specified transfer certificate.
     */
    public function edit(TransferCertificate $transferCertificate)
    {
        $students = Student::with(['classSection', 'classSection.class.grade'])
            ->orderBy('first_name')
            ->get();
            
        $classes = Classes::with(['grade', 'stream'])->get();
        $sections = ClassSection::with(['class.grade', 'class.stream'])->get();
            
        return view('admin.transfer-certificates.edit', compact('transferCertificate', 'students', 'classes', 'sections'));
    }

    /**
     * Update the specified transfer certificate in storage.
     */
    public function update(Request $request, TransferCertificate $transferCertificate)
    {
        $request->validate([
            'certificate_types' => 'required|array',
            'certificate_types.*' => 'string',
            'student_status_after' => 'required|in:active,inactive',
            'remarks' => 'nullable|string',
            'issued_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $transferCertificate->update([
                'certificate_types' => json_encode($request->certificate_types),
                'student_status_after' => $request->student_status_after,
                'remarks' => $request->remarks,
                'issued_date' => $request->issued_date,
            ]);

            // Update student status
            $student = Student::find($transferCertificate->student_id);
            if ($student) {
                if ($request->student_status_after === 'inactive') {
                    $student->update(['status' => 'Inactive']);
                } else {
                    $student->update(['status' => 'Active']);
                }
            }

            DB::commit();

            return redirect()->route('admin.transfer-certificates.index')
                ->with('success', 'Transfer certificate updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update transfer certificate: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified transfer certificate from storage.
     */
    public function destroy(TransferCertificate $transferCertificate)
    {
        try {
            $certificateNumber = $transferCertificate->certificate_number;
            
            // Get student to revert status
            $student = Student::find($transferCertificate->student_id);
            
            $transferCertificate->delete();

            // If student has no other transfer certificates, revert status to Active
            if ($student) {
                $hasOtherCertificates = TransferCertificate::where('student_id', $student->id)->exists();
                if (!$hasOtherCertificates) {
                    $student->update(['status' => 'Active']);
                }
            }

            return redirect()->route('admin.transfer-certificates.index')
                ->with('success', 'Transfer certificate ' . $certificateNumber . ' deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete transfer certificate: ' . $e->getMessage());
        }
    }

    /**
     * Get student data for AJAX request.
     */
    public function getStudentData($studentId)
    {
        $student = Student::with(['classSection', 'classSection.class.grade', 'classSection.class.stream'])
            ->findOrFail($studentId);
            
        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'admission_number' => $student->admission_number,
                'roll_number' => $student->roll_number,
                'section' => $student->classSection->section_name ?? 'N/A',
                'grade' => optional($student->classSection->class)->grade->name ?? 'N/A',
                'class' => optional($student->classSection->class)->grade->name ?? 'N/A',
                'gender' => $student->gender,
                'date_of_birth' => $student->date_of_birth,
                'father_name' => $student->father_name,
                'mother_name' => $student->mother_name,
                'address' => $student->address,
                'phone' => $student->phone,
            ]
        ]);
    }

    /**
     * Get sections by class for AJAX request.
     */
    public function getSections($classId)
    {
        $sections = ClassSection::where('class_id', $classId)
            ->with(['class.grade'])
            ->get()
            ->map(function($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->section_name,
                    'grade' => optional($section->class)->grade->name ?? 'N/A',
                ];
            });
            
        return response()->json([
            'success' => true,
            'sections' => $sections
        ]);
    }

    /**
     * Get students by section for AJAX request.
     */
    public function getStudents($sectionId)
    {
        $students = Student::where('class_section_id', $sectionId)
            ->where('status', 'Active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_number', 'roll_number']);
            
        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Download transfer certificate PDF.
     */
    public function download($id)
    {
        $certificate = TransferCertificate::with(['student', 'student.classSection', 'student.classSection.class.grade'])
            ->findOrFail($id);
            
        // You can implement PDF generation here using DomPDF or similar
        // For now, redirect back with message
        return redirect()->back()->with('info', 'PDF download feature coming soon.');
    }
}