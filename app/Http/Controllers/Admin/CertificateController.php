<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateType;
use App\Models\CertificateDistribution;
use App\Models\Student;
use App\Models\ClassSection;        // ✅ Changed from Classes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Display list of certificate types with distribution counts.
     */
    public function index()
    {
        $certificateTypes = CertificateType::withCount('distributions')->get();
        return view('admin.certificates.index', compact('certificateTypes'));
    }

    /**
     * Show form to create a new certificate type.
     */
    public function createType()
    {
        return view('admin.certificates.create-type');
    }

    /**
     * Store a new certificate type with optional template file.
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255|unique:certificate_types',
            'description'   => 'nullable|string',
            'template_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'is_active'     => 'boolean',
        ]);

        $data = $request->except('template_file');
        if ($request->hasFile('template_file')) {
            $data['template_file'] = $request->file('template_file')->store('certificates/templates', 'public');
        }
        $data['is_active'] = $request->has('is_active');

        CertificateType::create($data);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate type created successfully.');
    }

    /**
     * Show form to edit an existing certificate type.
     */
    public function editType(CertificateType $certificateType)
    {
        return view('admin.certificates.edit-type', compact('certificateType'));
    }

    /**
     * Update certificate type – title, description, template, active status.
     */
    public function updateType(Request $request, CertificateType $certificateType)
    {
        $request->validate([
            'title'         => 'required|string|max:255|unique:certificate_types,title,' . $certificateType->id,
            'description'   => 'nullable|string',
            'template_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'is_active'     => 'boolean',
        ]);

        $data = $request->except('template_file');
        if ($request->hasFile('template_file')) {
            if ($certificateType->template_file) {
                Storage::disk('public')->delete($certificateType->template_file);
            }
            $data['template_file'] = $request->file('template_file')->store('certificates/templates', 'public');
        } elseif ($request->remove_template == '1') {
            if ($certificateType->template_file) {
                Storage::disk('public')->delete($certificateType->template_file);
            }
            $data['template_file'] = null;
        }

        $data['is_active'] = $request->has('is_active');
        $certificateType->update($data);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate type updated.');
    }

    /**
     * Delete a certificate type and its associated template file.
     */
    public function destroyType(CertificateType $certificateType)
    {
        if ($certificateType->template_file) {
            Storage::disk('public')->delete($certificateType->template_file);
        }
        $certificateType->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate type deleted.');
    }

    /**
     * Show form to distribute a certificate to a student.
     * ✅ Now uses ClassSection instead of Classes.
     */
    public function distributeForm(CertificateType $certificateType)
    {
        $classSections = ClassSection::with('class.grade')->get();
        return view('admin.certificates.distribute', compact('certificateType', 'classSections'));
    }

    /**
     * Process certificate distribution (issue to student).
     */
    public function distribute(Request $request, CertificateType $certificateType)
    {
        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'issue_date'       => 'required|date',
            'remarks'          => 'nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'certificate_type_id' => $certificateType->id,
            'student_id'          => $request->student_id,
            'issue_date'          => $request->issue_date,
            'remarks'             => $request->remarks,
        ];

        if ($request->hasFile('certificate_file')) {
            $data['certificate_file'] = $request->file('certificate_file')->store('certificates/issued', 'public');
        } else {
            $data['certificate_file'] = null;
        }

        CertificateDistribution::create($data);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate distributed successfully.');
    }

    /**
     * Show distribution history with pagination.
     */
    public function history()
    {
        $distributions = CertificateDistribution::with(['certificateType', 'student'])
            ->latest()
            ->paginate(20);
        return view('admin.certificates.history', compact('distributions'));
    }

    /**
     * Download the issued certificate file (or fallback to template if available).
     */
    public function download(CertificateDistribution $distribution)
    {
        if ($distribution->certificate_file && Storage::disk('public')->exists($distribution->certificate_file)) {
            return response()->download(storage_path('app/public/' . $distribution->certificate_file));
        }

        $templateFile = $distribution->certificateType->template_file;
        if ($templateFile && Storage::disk('public')->exists($templateFile)) {
            return response()->download(storage_path('app/public/' . $templateFile));
        }

        return back()->with('error', 'No certificate file available for download.');
    }

    // ------------------------------------------------------------------
    // ✅ Updated AJAX endpoints using class_section_id
    // ------------------------------------------------------------------

    /**
     * Get students by class_section_id (replaces old getSections + getStudents)
     */
    public function getStudentsBySection($classSectionId)
    {
        $students = Student::where('class_section_id', $classSectionId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'admission_number']);

        $data = $students->map(fn($student) => [
            'id' => $student->id,
            'name' => $student->full_name,
            'admission_number' => $student->admission_number,
        ]);

        return response()->json($data);
    }

    /**
     * Get full student info (now includes class section details)
     */
    public function getStudentInfo($studentId)
    {
        $student = Student::with('classSection.class.grade', 'classSection.class.stream')
            ->findOrFail($studentId);

        $classSection = $student->classSection;
        $className = $classSection?->class?->full_name ?? 'N/A';
        $sectionName = $classSection?->section_name ?? 'N/A';

        return response()->json([
            'name' => $student->full_name,
            'admission_number' => $student->admission_number,
            'class' => $className,
            'section' => $sectionName,
        ]);
    }
}