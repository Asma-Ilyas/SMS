<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\StudentTransfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        $outgoingTransfers = StudentTransfer::with('student')
            ->where('transfer_type', 'outgoing')
            ->latest()
            ->paginate(20);

        $incomingTransfers = StudentTransfer::with('student')
            ->where('transfer_type', 'incoming')
            ->latest()
            ->paginate(20);

        return view('admin.transfers.index', compact('outgoingTransfers', 'incomingTransfers'));
    }

    // Form to transfer a student out (no changes – uses existing student)
    public function transferOutForm(Student $student)
    {
        return view('admin.transfers.transfer-out', compact('student'));
    }

    // Process outgoing transfer (no changes – student already linked to class_section)
    public function transferOut(Request $request, Student $student)
    {
        $request->validate([
            'to_school'     => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'reason'        => 'nullable|string',
            'document'      => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('transfers', 'public');
        }

        StudentTransfer::create([
            'student_id'    => $student->id,
            'transfer_type' => 'outgoing',
            'to_school'     => $request->to_school,
            'transfer_date' => $request->transfer_date,
            'reason'        => $request->reason,
            'document_path' => $documentPath,
        ]);

        $student->status = 'Inactive';
        $student->save();

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student transferred out successfully.');
    }

    // Show form for incoming transfer – now uses class_sections
    public function transferInForm()
    {
        $classSections = ClassSection::with('class.grade', 'class.stream')
            ->orderBy('class_id')
            ->orderBy('section_name')
            ->get();
        return view('admin.transfers.transfer-in', compact('classSections'));
    }

    // Process incoming transfer (creates a new student using class_section_id)
    public function transferInStore(Request $request)
    {
        $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'gender'            => 'required|in:Male,Female',
            'date_of_birth'     => 'required|date',
            'admission_number'  => 'required|unique:students,admission_number',
            'class_section_id'  => 'required|exists:class_sections,id',   // ✅ new foreign key
            'father_name'       => 'required|string|max:100',
            'from_school'       => 'required|string|max:255',
            'transfer_date'     => 'required|date',
            'reason'            => 'nullable|string',
            // Optional fields you may want to include:
            // 'admission_date', 'roll_number', 'profile_photo', etc.
        ]);

        // Prepare student data (only fields that exist in your `students` table)
        $studentData = $request->only([
            'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth',
            'admission_number', 'class_section_id',   // ✅ replaced class_id + section
            'father_name', 'father_phone',
            'mother_name', 'mother_phone', 'status', 'admission_date'
        ]);

        // Set default status if not provided
        if (empty($studentData['status'])) {
            $studentData['status'] = 'Active';
        }
        if (empty($studentData['admission_date'])) {
            $studentData['admission_date'] = now()->toDateString();
        }

        $student = Student::create($studentData);

        // Record incoming transfer
        StudentTransfer::create([
            'student_id'    => $student->id,
            'transfer_type' => 'incoming',
            'from_school'   => $request->from_school,
            'transfer_date' => $request->transfer_date,
            'reason'        => $request->reason,
        ]);

        return redirect()->route('admin.transfers.index')
            ->with('success', 'Student transferred in and admitted successfully.');
    }
}