<?php
// app/Http/Controllers/Admin/StudentFeeSubmissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FeeType;
use App\Models\Classes;
use App\Models\StudentFeeSubmission;
use Illuminate\Http\Request;

class StudentFeeSubmissionController extends Controller
{
    public function index()
    {
        $submissions = StudentFeeSubmission::with(['student', 'feeType'])->latest()->paginate(20);
        return view('admin.fees.submissions', compact('submissions'));
    }

    public function create()
    {
        $classes = Classes::with('grade')->get();
        $feeTypes = FeeType::where('is_active', true)->get();
        return view('admin.fees.submission-create', compact('classes', 'feeTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id'          => 'required|exists:classes,id',
            'section'           => 'required|string',
            'student_id'        => 'required|exists:students,id',
            'fee_type_id'       => 'required|exists:fee_types,id',
            'period'            => 'required|in:monthly,quarterly,annually,one_time',
            'amount'            => 'required|numeric|min:0',
            'submission_date'   => 'required|date',
            'receipt_number'    => 'nullable|string',
            'remarks'           => 'nullable|string',
        ]);

        StudentFeeSubmission::create([
            'student_id'       => $request->student_id,
            'fee_type_id'      => $request->fee_type_id,
            'period'           => $request->period,
            'amount'           => $request->amount,
            'submission_date'  => $request->submission_date,
            'receipt_number'   => $request->receipt_number,
            'remarks'          => $request->remarks,
        ]);

        return redirect()->route('admin.fee-submissions.index')
            ->with('success', 'Fee payment recorded.');
    }

    public function show(StudentFeeSubmission $feeSubmission)
    {
        return view('admin.fees.submission-show', compact('feeSubmission'));
    }

    public function edit(StudentFeeSubmission $feeSubmission)
    {
        $classes = Classes::with('grade')->get();
        $feeTypes = FeeType::where('is_active', true)->get();
        $selectedStudent = $feeSubmission->student;
        return view('admin.fees.submission-edit', compact('feeSubmission', 'classes', 'feeTypes', 'selectedStudent'));
    }

    public function update(Request $request, StudentFeeSubmission $feeSubmission)
    {
        $request->validate([
            'class_id'          => 'required|exists:classes,id',
            'section'           => 'required|string',
            'student_id'        => 'required|exists:students,id',
            'fee_type_id'       => 'required|exists:fee_types,id',
            'period'            => 'required|in:monthly,quarterly,annually,one_time',
            'amount'            => 'required|numeric|min:0',
            'submission_date'   => 'required|date',
            'receipt_number'    => 'nullable|string',
            'remarks'           => 'nullable|string',
        ]);

        $feeSubmission->update([
            'student_id'       => $request->student_id,
            'fee_type_id'      => $request->fee_type_id,
            'period'           => $request->period,
            'amount'           => $request->amount,
            'submission_date'  => $request->submission_date,
            'receipt_number'   => $request->receipt_number,
            'remarks'          => $request->remarks,
        ]);

        return redirect()->route('admin.fee-submissions.index')
            ->with('success', 'Fee payment updated.');
    }

    public function destroy(StudentFeeSubmission $feeSubmission)
    {
        $feeSubmission->delete();
        return redirect()->route('admin.fee-submissions.index')
            ->with('success', 'Fee payment deleted.');
    }
}