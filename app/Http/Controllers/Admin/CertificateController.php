<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateType;
use App\Models\CertificateDistribution;
use App\Models\Student;
use App\Models\Classes;
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
            // Delete old template if exists
            if ($certificateType->template_file) {
                Storage::disk('public')->delete($certificateType->template_file);
            }
            $data['template_file'] = $request->file('template_file')->store('certificates/templates', 'public');
        } elseif ($request->remove_template == '1') {
            // Allow removal of existing template
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
        // Delete template file if exists
        if ($certificateType->template_file) {
            Storage::disk('public')->delete($certificateType->template_file);
        }
        // Delete all distributions (cascaded via foreign key)
        $certificateType->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate type deleted.');
    }

    /**
     * Show form to distribute a certificate to a student.
     */
    public function distributeForm(CertificateType $certificateType)
{
    $classes = Classes::with(['grade', 'stream'])->get();
    return view('admin.certificates.distribute', compact('certificateType', 'classes'));
}

    /**
     * Process certificate distribution (issue to student).
     * Optionally upload a custom certificate file (otherwise fallback to template).
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

        // If user uploads a custom certificate, store it
        if ($request->hasFile('certificate_file')) {
            $data['certificate_file'] = $request->file('certificate_file')->store('certificates/issued', 'public');
        } else {
            // Optionally use the template file if no custom file was provided
            // (we store null; the download method will fallback to template)
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
        // First try to download the specific issued file
        if ($distribution->certificate_file && Storage::disk('public')->exists($distribution->certificate_file)) {
            return response()->download(storage_path('app/public/' . $distribution->certificate_file));
        }

        // Fallback to the certificate type's template
        $templateFile = $distribution->certificateType->template_file;
        if ($templateFile && Storage::disk('public')->exists($templateFile)) {
            return response()->download(storage_path('app/public/' . $templateFile));
        }

        return back()->with('error', 'No certificate file available for download.');
    }

    public function getSections($classId)
{
    // Return distinct sections for a given class from students table
    $sections = Student::where('class_id', $classId)->distinct()->pluck('section');
    return response()->json($sections);
}

public function getStudents($classId, $section)
{
    $students = Student::where('class_id', $classId)
        ->where('section', $section)
        ->get(['id', 'first_name', 'middle_name', 'last_name', 'admission_number']);
    
    $data = $students->map(function($student) {
        return [
            'id' => $student->id,
            'name' => $student->full_name,
            'admission_number' => $student->admission_number,
        ];
    });
    return response()->json($data);
}

public function getStudentInfo($studentId)
{
    $student = Student::with('class.grade', 'class.stream')->findOrFail($studentId);
    return response()->json([
        'name' => $student->full_name,
        'admission_number' => $student->admission_number,
        'class' => $student->class->full_name,
        'section' => $student->section,
    ]);
}
}