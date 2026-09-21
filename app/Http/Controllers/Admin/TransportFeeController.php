<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransportFeeTypeRequest;
use App\Http\Requests\UpdateTransportFeeTypeRequest;
use App\Models\TransportFeeType;
use App\Models\StudentTransport;
use App\Models\StudentTransportFeePayment;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportFeeController extends Controller
{
    // ---- Fee Types ----
    public function index()
    {
        $feeTypes = TransportFeeType::with('route')->latest()->paginate(20);
        return view('admin.transport.fees.types-index', compact('feeTypes'));
    }

    public function create()
    {
        $routes = TransportRoute::orderBy('name')->get();
        return view('admin.transport.fees.types-create', compact('routes'));
    }

    public function store(StoreTransportFeeTypeRequest $request)
    {
        TransportFeeType::create($request->validated());

        return redirect()->route('admin.transport-fee-types.index')->with('success', 'Fee type created successfully.');
    }

    public function edit(TransportFeeType $transportFeeType)
    {
        $routes = TransportRoute::orderBy('name')->get();
        return view('admin.transport.fees.types-edit', ['feeType' => $transportFeeType, 'routes' => $routes]);
    }

    public function update(UpdateTransportFeeTypeRequest $request, TransportFeeType $transportFeeType)
    {
        $transportFeeType->update($request->validated());

        return redirect()->route('admin.transport-fee-types.index')->with('success', 'Fee type updated successfully.');
    }

    public function destroy(TransportFeeType $transportFeeType)
    {
        if ($transportFeeType->route_id && StudentTransport::where('transport_fee_type_id', $transportFeeType->id)->exists()) {
            return back()->with('error', 'Cannot delete a fee type currently used by student assignments.');
        }

        $transportFeeType->delete();

        return redirect()->route('admin.transport-fee-types.index')->with('success', 'Fee type deleted successfully.');
    }

    // ---- Fee Payments ----
    public function payments()
    {
        $payments = StudentTransportFeePayment::with('studentTransport.student')->latest()->paginate(20);
        return view('admin.transport.fees.payments-index', compact('payments'));
    }

    public function generateMonthly(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $month = $request->month;

        $count = 0;

        DB::transaction(function () use ($month, &$count) {
            StudentTransport::active()->with('feeType')->chunkById(50, function ($assignments) use ($month, &$count) {
                foreach ($assignments as $assignment) {
                    if (!$assignment->feeType) {
                        continue;
                    }

                    $exists = StudentTransportFeePayment::where('student_transport_id', $assignment->id)
                        ->where('month', $month)->exists();

                    if ($exists) {
                        continue;
                    }

                    StudentTransportFeePayment::create([
                        'student_transport_id' => $assignment->id,
                        'month' => $month,
                        'amount' => $assignment->feeType->amount,
                        'due_date' => \Carbon\Carbon::parse($month . '-05'),
                        'status' => 'pending',
                    ]);
                    $count++;
                }
            });
        });

        return back()->with('success', "{$count} fee record(s) generated for {$month}.");
    }

    public function pay(Request $request, StudentTransportFeePayment $payment)
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
