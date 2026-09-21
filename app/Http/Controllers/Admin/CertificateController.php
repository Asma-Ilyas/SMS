<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateDistribution;
use App\Models\CertificateType;
use App\Models\ClassSection;
use App\Models\Student;
use App\Models\User;
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
        $data = $request->validate([
            'title'         => 'required|string|max:255|unique:certificate_types,title',
            'description'   => 'nullable|string',
            'template_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('template_file')) {
            $data['template_file'] = $request->file('template_file')->store('certificates/templates', 'public');
        }

        // Checkbox: missing = false, "on"/"1" = true
        $data['is_active'] = $request->boolean('is_active');

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
     * Update certificate type: title, description, template, active status.
     */
    public function updateType(Request $request, CertificateType $certificateType)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255|unique:certificate_types,title,' . $certificateType->id,
            'description'   => 'nullable|string',
            'template_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('template_file')) {
            if ($certificateType->template_file) {
                Storage::disk('public')->delete($certificateType->template_file);
            }
            $data['template_file'] = $request->file('template_file')->store('certificates/templates', 'public');
        } elseif ($request->input('remove_template') == '1') {
            if ($certificateType->template_file) {
                Storage::disk('public')->delete($certificateType->template_file);
            }
            $data['template_file'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');

        $certificateType->update($data);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate type updated.');
    }

    /**
     * Delete a certificate type and its template file.
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
     */
    public function distributeForm(CertificateType $certificateType)
    {
        $classSections = ClassSection::with('class.grade')
            ->orderBy('class_id')
            ->orderBy('section_name')
            ->get();

        return view('admin.certificates.distribute', compact('certificateType', 'classSections'));
    }

    /**
     * Issue the certificate to a student and notify them.
     */
    public function distribute(Request $request, CertificateType $certificateType)
    {
        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'issue_date'       => 'required|date',
            'remarks'          => 'nullable|string|max:1000',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'certificate_type_id' => $certificateType->id,
            'student_id'          => $request->student_id,
            'issue_date'          => $request->issue_date,
            'remarks'             => $request->remarks,
            'certificate_file'    => $request->hasFile('certificate_file')
                ? $request->file('certificate_file')->store('certificates/issued', 'public')
                : null,
        ];

        $distribution = CertificateDistribution::create($data);

        $this->notifyStudent($distribution, $certificateType);

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
     * Download the issued certificate file (falls back to the template).
     */
    public function download(CertificateDistribution $distribution)
    {
        $disk = Storage::disk('public');

        if ($distribution->certificate_file && $disk->exists($distribution->certificate_file)) {
            return response()->download($disk->path($distribution->certificate_file));
        }

        $template = $distribution->certificateType?->template_file;
        if ($template && $disk->exists($template)) {
            return response()->download($disk->path($template));
        }

        return back()->with('error', 'No certificate file available for download.');
    }

    // ------------------------------------------------------------------
    // AJAX endpoints
    // ------------------------------------------------------------------

    /**
     * Students of one class section, as JSON.
     */
    public function getStudentsBySection($classSectionId)
    {
        $students = Student::where('class_section_id', $classSectionId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'admission_number']);

        return response()->json($students->map(fn ($s) => [
            'id'               => $s->id,
            'name'             => $this->studentName($s),
            'admission_number' => $s->admission_number,
        ])->values());
    }

    /**
     * Basic student details plus class and section, as JSON.
     */
    public function getStudentInfo($studentId)
    {
        $student = Student::findOrFail($studentId);
        $section = ClassSection::with('class.grade')->find($student->class_section_id);

        return response()->json([
            'name'             => $this->studentName($student),
            'admission_number' => $student->admission_number,
            'class'            => $section?->class?->grade?->name ?? 'N/A',
            'section'          => $section?->section_name ?? 'N/A',
        ]);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function studentName($student): string
    {
        return trim(implode(' ', array_filter([
            $student->first_name,
            $student->middle_name,
            $student->last_name,
        ])));
    }

    /**
     * Tell the student their certificate was issued.
     * Never blocks the issue if the notification fails.
     */
    private function notifyStudent(CertificateDistribution $distribution, CertificateType $type): void
    {
        try {
            if (! class_exists(\App\Notifications\AdminAlert::class)) {
                return;
            }

            $student = Student::find($distribution->student_id);
            if (! $student) {
                return;
            }

            // Prefer a direct link if users.id is stored on the student, otherwise match by email.
            $user = $student->user_id
                ? User::find($student->user_id)
                : ($student->email ? User::where('email', $student->email)->first() : null);

            $user?->notify(new \App\Notifications\AdminAlert(
                'Certificate issued',
                $type->title . ' has been issued to you.',
                route('student.certificates.index'),
                'certificate',
                'success'
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}