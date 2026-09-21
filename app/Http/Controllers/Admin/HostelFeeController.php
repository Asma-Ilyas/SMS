<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHostelFeeTypeRequest;
use App\Http\Requests\UpdateHostelFeeTypeRequest;
use App\Models\HostelFeeType;
use App\Models\StudentHostelAllocation;
use App\Models\StudentHostelFeePayment;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelFeeController extends Controller
{
    // ---- Fee Types ----
    public function index()
    {
        $feeTypes = HostelFeeType::with('hostel')->latest()->paginate(20);
        return view('admin.hostel.fees.types-index', compact('feeTypes'));
    }

    public function create()
    {
        $hostels = Hostel::orderBy('name')->get();
        return view('admin.hostel.fees.types-create', compact('hostels'));
    }

    public function store(StoreHostelFeeTypeRequest $request)
    {
        HostelFeeType::create($request->validated());

        return redirect()->route('admin.hostel-fee-types.index')->with('success', 'Fee type created successfully.');
    }

    public function edit(HostelFeeType $hostelFeeType)
    {
        $hostels = Hostel::orderBy('name')->get();
        return view('admin.hostel.fees.types-edit', ['feeType' => $hostelFeeType, 'hostels' => $hostels]);
    }

    public function update(UpdateHostelFeeTypeRequest $request, HostelFeeType $hostelFeeType)
    {
        $hostelFeeType->update($request->validated());

        return redirect()->route('admin.hostel-fee-types.index')->with('success', 'Fee type updated successfully.');
    }

    public function destroy(HostelFeeType $hostelFeeType)
    {
        if (StudentHostelAllocation::where('hostel_fee_type_id', $hostelFeeType->id)->exists()) {
            return back()->with('error', 'Cannot delete a fee type currently used by student allocations.');
        }

        $hostelFeeType->delete();

        return redirect()->route('admin.hostel-fee-types.index')->with('success', 'Fee type deleted successfully.');
    }

    // ---- Fee Payments ----
    public function payments()
    {
        $payments = StudentHostelFeePayment::with('allocation.student')->latest()->paginate(20);
        return view('admin.hostel.fees.payments-index', compact('payments'));
    }

    public function generateMonthly(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $month = $request->month;

        $count = 0;

        DB::transaction(function () use ($month, &$count) {
            StudentHostelAllocation::active()->with('feeType')->chunkById(50, function ($allocations) use ($month, &$count) {
                foreach ($allocations as $allocation) {
                    if (!$allocation->feeType) {
                        continue;
                    }

                    $exists = StudentHostelFeePayment::where('student_hostel_allocation_id', $allocation->id)
                        ->where('month', $month)->exists();

                    if ($exists) {
                        continue;
                    }

                    StudentHostelFeePayment::create([
                        'student_hostel_allocation_id' => $allocation->id,
                        'month' => $month,
                        'amount' => $allocation->feeType->amount,
                        'due_date' => \Carbon\Carbon::parse($month . '-05'),
                        'status' => 'pending',
                    ]);
                    $count++;
                }
            });
        });

        return back()->with('success', "{$count} fee record(s) generated for {$month}.");
    }

    public function pay(Request $request, StudentHostelFeePayment $payment)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01|max:' . ($payment->amount - $payment->paid_amount),
            'payment_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        $newPaid = $payment->paid_amount + $request->paid_amount;
        $status = $newPaid >= $payment->amount ? 'paid' : 'partial';

        $payment->update([
            'paid_amount' => $newPaid,
            'status' => $status,
            'payment_date' => $request->payment_date,
            'receipt_number' => $request->receipt_number,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }
}
