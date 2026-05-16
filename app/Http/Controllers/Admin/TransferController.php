<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
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

    // Form to transfer a student out
    public function transferOutForm(Student $student)
    {
        return view('admin.transfers.transfer-out', compact('student'));
    }

    // Process outgoing transfer
    public function transferOut(Request $request, Student $student)
    {
        $request->validate([
            'to_school' => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'reason' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('transfers', 'public');
        }

        StudentTransfer::create([
            'student_id' => $student->id,
            'transfer_type' => 'outgoing',
            'to_school' => $request->to_school,
            'transfer_date' => $request->transfer_date,
            'reason' => $request->reason,
            'document_path' => $documentPath,
        ]);

        // Update student status to Inactive (or 'transferred')
        $student->status = 'Inactive';
        $student->save();

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student transferred out successfully.');
    }

    
 
public function transferInForm()
{
    $classes = Classes::with(['grade', 'stream', 'electiveTrack'])->get();
    return view('admin.transfers.transfer-in', compact('classes'));
}

    // Process incoming transfer (creates new student)
    public function transferInStore(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'admission_number' => 'required|unique:students',
            'class_id' => 'required|exists:classes,id',
            'section' => 'required|string|max:10',
            'father_name' => 'required|string|max:100',
            'from_school' => 'required|string|max:255',
            'transfer_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        // Create student
        $student = Student::create($request->only([
            'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth',
            'admission_number', 'class_id', 'section', 'father_name', 'father_phone',
            'mother_name', 'mother_phone', 'status', 'admission_date'
        ]));

        // Record incoming transfer
        StudentTransfer::create([
            'student_id' => $student->id,
            'transfer_type' => 'incoming',
            'from_school' => $request->from_school,
            'transfer_date' => $request->transfer_date,
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.transfers.index')
            ->with('success', 'Student transferred in and admitted successfully.');
    }
}