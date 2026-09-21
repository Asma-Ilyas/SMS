<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\FeeSubmissionType;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\StudentFeeSubmission;
use App\Support\PortalAlert;
use Illuminate\Http\Request;

class FeeInstallmentController extends Controller
{
    /**
     * Display a listing of fee installments
     */
    public function index()
    {
        $installments = StudentFeeSubmission::with(['student', 'feeSubmissionType', 'invoices'])
            ->latest()
            ->paginate(20);

        return view('admin.fees.installments.index', compact('installments'));
    }

    /**
     * Show form to create a new installment plan
     */
    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $feeTypes = FeeSubmissionType::where('is_active', true)->orderBy('name')->get();
        $banks = Bank::where('is_active', true)->get();

        return view('admin.fees.installments.create', compact('students', 'feeTypes', 'banks'));
    }

    /**
     * Store a newly created installment and tell the student.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'             => 'required|exists:students,id',
            'fee_submission_type_id' => 'required|exists:fee_submission_types,id',
            'installment_number'     => 'required|integer|min:1',
            'amount'                 => 'required|numeric|min:0',
            'due_date'               => 'required|date',
            'status'                 => 'nullable|in:pending,partial,paid',
        ]);

        $installment = StudentFeeSubmission::create($data);

        // Generate invoice
        $this->generateInvoice($installment);

        PortalAlert::toStudent(
            $installment->student_id,
            'New fee due',
            'A fee of Rs. ' . number_format((float) $installment->amount, 2)
                . ' is due on ' . \Carbon\Carbon::parse($installment->due_date)->format('d M Y') . '.',
            PortalAlert::link('student.fees.index', '/student/fees'),
            'fee',
            'info'
        );

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment created successfully.');
    }

    /**
     * Display the specified installment
     */
    public function show($id)
    {
        $installment = StudentFeeSubmission::with(['student', 'feeSubmissionType', 'invoices', 'invoices.bank'])
            ->findOrFail($id);

        return view('admin.fees.installments.show', compact('installment'));
    }

    /**
     * Show form to edit installment
     */
    public function edit($id)
    {
        $installment = StudentFeeSubmission::findOrFail($id);
        $students = Student::orderBy('first_name')->get();
        $feeTypes = FeeSubmissionType::where('is_active', true)->orderBy('name')->get();
        $banks = Bank::where('is_active', true)->get();

        return view('admin.fees.installments.edit', compact('installment', 'students', 'feeTypes', 'banks'));
    }

    /**
     * Update the specified installment
     */
    public function update(Request $request, $id)
    {
        $installment = StudentFeeSubmission::findOrFail($id);

        $data = $request->validate([
            'student_id'             => 'required|exists:students,id',
            'fee_submission_type_id' => 'required|exists:fee_submission_types,id',
            'installment_number'     => 'required|integer|min:1',
            'amount'                 => 'required|numeric|min:0',
            'due_date'               => 'required|date',
            'status'                 => 'nullable|in:pending,partial,paid',
        ]);

        $installment->update($data);

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment updated successfully.');
    }

    /**
     * Delete the specified installment
     */
    public function destroy($id)
    {
        $installment = StudentFeeSubmission::findOrFail($id);

        // Delete associated invoices first
        $installment->invoices()->delete();
        $installment->delete();

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment deleted successfully.');
    }

    /**
     * Show payment form
     */
    public function payForm($id)
    {
        $installment = StudentFeeSubmission::with(['student', 'feeSubmissionType'])
            ->findOrFail($id);

        $banks = Bank::where('is_active', true)->get();

        return view('admin.fees.installments.pay', compact('installment', 'banks'));
    }

    /**
     * Process a payment (full or partial) and tell the student.
     */
    public function pay(Request $request, $id)
    {
        $installment = StudentFeeSubmission::findOrFail($id);

        $request->validate([
            'payment_date'   => 'required|date',
            'paid_amount'    => 'required|numeric|min:0|max:' . ($installment->amount - $installment->paid_amount),
            'payment_method' => 'required|in:bank,cash,cheque',
            'bank_id'        => 'nullable|exists:banks,id',
            'receipt_number' => 'nullable|string|max:100',
            'remarks'        => 'nullable|string|max:500',
        ]);

        $paidNow       = (float) $request->paid_amount;
        $newPaidAmount = $installment->paid_amount + $paidNow;

        $status = 'partial';
        if ($newPaidAmount >= $installment->amount) {
            $status = 'paid';
            $newPaidAmount = $installment->amount;
        }

        $installment->update([
            'paid_amount'  => $newPaidAmount,
            'status'       => $status,
            'payment_date' => $request->payment_date,
        ]);

        // Update associated invoice.
        // NOTE: invoices.status only allows pending/paid/overdue/cancelled, so a
        // partial payment keeps the invoice "pending" instead of writing "partial".
        $invoice = $installment->invoices()->first();
        if ($invoice) {
            $invoice->update([
                'status'          => $status === 'paid' ? 'paid' : 'pending',
                'paid_at'         => $status === 'paid' ? $request->payment_date : null,
                'payment_remarks' => $request->remarks,
            ]);
        }

        PortalAlert::toStudent(
            $installment->student_id,
            $status === 'paid' ? 'Payment received' : 'Partial payment received',
            'We received Rs. ' . number_format($paidNow, 2) . ' towards your fee.'
                . ($status === 'paid' ? ' It is now fully paid.' : ' Remaining: Rs. ' . number_format($installment->amount - $newPaidAmount, 2) . '.'),
            PortalAlert::link('student.fees.index', '/student/fees'),
            'fee',
            'success'
        );

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Payment processed successfully.');
    }

    /**
     * Generate invoice for installment
     */
    protected function generateInvoice($installment)
    {
        $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);

        Invoice::create([
            'invoice_number'            => $invoiceNumber,
            'student_id'                => $installment->student_id,
            'bank_id'                   => null,
            'student_fee_submission_id' => $installment->id,
            'amount'                    => $installment->amount,
            'discount_amount'           => 0,
            'due_date'                  => $installment->due_date,
            'status'                    => 'pending',
            'created_at'                => now(),
            'updated_at'                => now(),
        ]);
    }

    /**
     * Download challan
     */
    public function downloadChallan($id)
    {
        $installment = StudentFeeSubmission::with(['student', 'invoices'])
            ->findOrFail($id);

        $invoice = $installment->invoices()->first();

        if (!$invoice) {
            return back()->with('error', 'No invoice found for this installment.');
        }

        // Generate PDF logic here
        // return PDF::download(...);

        return back()->with('info', 'Challan download functionality will be implemented.');
    }

    /**
     * Upload payment proof: tell every admin.
     */
    public function uploadProof(Request $request, $id)
    {
        $installment = StudentFeeSubmission::findOrFail($id);

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $invoice = $installment->invoices()->first();
        if ($invoice) {
            $invoice->update(['payment_proof_file' => $path]);
        }

        $student = Student::find($installment->student_id);
        $studentName = trim(($student?->first_name ?? '') . ' ' . ($student?->last_name ?? '')) ?: 'A student';

        PortalAlert::toAdmins(
            'Payment proof uploaded',
            $studentName . ' uploaded a payment proof' . ($invoice ? ' for invoice ' . $invoice->invoice_number : '') . '. It needs approval.',
            PortalAlert::link('admin.fee-installments.show', '/admin/fee-installments/' . $installment->id, [$installment->id]),
            'fee',
            'info'
        );

        return back()->with('success', 'Payment proof uploaded successfully.');
    }

    /**
     * Approve payment: tell the student.
     */
    public function approvePayment($id)
    {
        $installment = StudentFeeSubmission::findOrFail($id);

        if ($installment->status === 'paid') {
            return back()->with('error', 'This installment is already paid.');
        }

        $installment->update([
            'status'       => 'paid',
            'paid_amount'  => $installment->amount,
            'payment_date' => now(),
        ]);

        $invoice = $installment->invoices()->first();
        if ($invoice) {
            $invoice->update([
                'status'      => 'paid',
                'paid_at'     => now(),
                'approved_by' => auth()->id(),
            ]);
        }

        PortalAlert::toStudent(
            $installment->student_id,
            'Payment approved',
            'Your fee payment has been approved.',
            PortalAlert::link('student.fees.index', '/student/fees'),
            'fee',
            'success'
        );

        return back()->with('success', 'Payment approved successfully.');
    }
}