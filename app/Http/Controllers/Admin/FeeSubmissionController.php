<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentFeeSubmission;
use App\Models\FeeSubmissionType;
use App\Models\Student;
use Illuminate\Http\Request;

class FeeSubmissionController extends Controller
{
    public function index()
    {
        $submissions = StudentFeeSubmission::with(['student', 'feeType'])
            ->latest()
            ->paginate(20);
        
        return view('admin.fees.submissions', compact('submissions'));
    }

    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        
        // FIXED: Use where instead of active()
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();
        
        return view('admin.fees.submission-create', compact('students', 'feeTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type_ids' => 'required|array|min:1',
            'fee_type_ids.*' => 'exists:fee_submission_types,id',
            'submission_date' => 'required|date',
            'receipt_number' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        foreach ($request->fee_type_ids as $feeTypeId) {
            $feeType = FeeSubmissionType::findOrFail($feeTypeId);
            StudentFeeSubmission::create([
                'student_id' => $request->student_id,
                'fee_submission_type_id' => $feeTypeId,
                'amount' => $feeType->amount,
                'submission_date' => $request->submission_date,
                'receipt_number' => $request->receipt_number,
                'remarks' => $request->remarks,
            ]);
        }

        return redirect()->route('admin.fee-submissions.index')
            ->with('success', 'Fee payment(s) recorded successfully.');
    }

    public function show($id)
    {
        $submission = StudentFeeSubmission::with(['student', 'feeType'])->findOrFail($id);
        return view('admin.fees.submission-show', compact('submission'));
    }

    public function edit($id)
    {
        $submission = StudentFeeSubmission::findOrFail($id);
        $students = Student::orderBy('first_name')->get();
        
        // FIXED: Use where instead of active()
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();
        
        return view('admin.fees.submission-edit', compact('submission', 'students', 'feeTypes'));
    }

    public function update(Request $request, $id)
    {
        $submission = StudentFeeSubmission::findOrFail($id);
        
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_submission_type_id' => 'required|exists:fee_submission_types,id',
            'amount' => 'required|numeric|min:0',
            'submission_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $submission->update($request->all());

        return redirect()->route('admin.fee-submissions.index')
            ->with('success', 'Fee submission updated.');
    }

    public function destroy($id)
    {
        $submission = StudentFeeSubmission::findOrFail($id);
        $submission->delete();

        return redirect()->route('admin.fee-submissions.index')
            ->with('success', 'Fee submission deleted.');
    }

    public function getFeeTypeAmount($feeTypeId)
    {
        $feeType = FeeSubmissionType::find($feeTypeId);
        
        if ($feeType) {
            return response()->json(['amount' => $feeType->amount]);
        }
        
        return response()->json(['amount' => 0]);
    }
}