<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Discount;
use App\Models\StudentDiscount;
use App\Models\FeeSubmissionType;
use Illuminate\Http\Request;

class StudentDiscountController extends Controller
{
    public function index()
    {
        $assignments = StudentDiscount::with(['student', 'discount', 'feeType'])->latest()->paginate(20);
        return view('admin.discounts.assignments', compact('assignments'));
    }

    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $discounts = Discount::where('is_active', true)->get();
        $feeTypes = FeeSubmissionType::where('is_active', true)->get();
        return view('admin.discounts.assign', compact('students', 'discounts', 'feeTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id',
            'discount_id'  => 'required|exists:discounts,id',
            'fee_submission_type_id' => 'nullable|exists:fee_submission_types,id',
            'valid_from'   => 'nullable|date',
            'valid_until'  => 'nullable|date|after_or_equal:valid_from',
        ]);

        StudentDiscount::create($request->all());

        return redirect()->route('admin.discount-assignments.index')
            ->with('success', 'Discount assigned to student.');
    }

    public function destroy(StudentDiscount $studentDiscount)
    {
        $studentDiscount->delete();
        return redirect()->route('admin.discount-assignments.index')
            ->with('success', 'Assignment removed.');
    }
}