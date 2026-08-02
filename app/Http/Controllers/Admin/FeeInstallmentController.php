<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentFeeSubmission; // ← Changed from StudentFeeInstallment
use App\Models\Student;
use App\Models\FeeSubmissionType;
use App\Models\Bank;
use App\Models\Invoice;
use Illuminate\Http\Request;

class FeeInstallmentController extends Controller
{
    /**
     * Display a listing of fee installments
     */
    public function index()
    {
        $installments = StudentFeeSubmission::with(['student', 'feeSubmissionType', 'invoices']) // ← Changed
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
     * Store a newly created installment
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_submission_type_id' => 'required|exists:fee_submission_types,id',
            'installment_number' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'nullable|in:pending,partial,paid',
        ]);

        $installment = StudentFeeSubmission::create($request->all()); // ← Changed

        // Generate invoice
        $this->generateInvoice($installment);

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment created successfully.');
    }

    /**
     * Display the specified installment
     */
    public function show($id)
    {
        $installment = StudentFeeSubmission::with(['student', 'feeSubmissionType', 'invoices', 'invoices.bank']) // ← Changed
            ->findOrFail($id);
            
        return view('admin.fees.installments.show', compact('installment'));
    }

    /**
     * Show form to edit installment
     */
    public function edit($id)
    {
        $installment = StudentFeeSubmission::findOrFail($id); // ← Changed
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
        $installment = StudentFeeSubmission::findOrFail($id); // ← Changed

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_submission_type_id' => 'required|exists:fee_submission_types,id',
            'installment_number' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'nullable|in:pending,partial,paid',
        ]);

        $installment->update($request->all());

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment updated successfully.');
    }

    /**
     * Delete the specified installment
     */
    public function destroy($id)
    {
        $installment = StudentFeeSubmission::findOrFail($id); // ← Changed
        
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
        $installment = StudentFeeSubmission::with(['student', 'feeSubmissionType']) // ← Changed
            ->findOrFail($id);
            
        $banks = Bank::where('is_active', true)->get();
        
        return view('admin.fees.installments.pay', compact('installment', 'banks'));
    }

    /**
     * Process payment
     */
    public function pay(Request $request, $id)
    {
        $installment = StudentFeeSubmission::findOrFail($id); // ← Changed

        $request->validate([
            'payment_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0|max:' . ($installment->amount - $installment->paid_amount),
            'payment_method' => 'required|in:bank,cash,cheque',
            'bank_id' => 'nullable|exists:banks,id',
            'receipt_number' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        $paidAmount = $request->paid_amount;
        $newPaidAmount = $installment->paid_amount + $paidAmount;
        
        $status = 'partial';
        if ($newPaidAmount >= $installment->amount) {
            $status = 'paid';
            $newPaidAmount = $installment->amount;
        }

        $installment->update([
            'paid_amount' => $newPaidAmount,
            'status' => $status,
            'payment_date' => $request->payment_date,
        ]);

        // Update associated invoice
        $invoice = $installment->invoices()->first();
        if ($invoice) {
            $invoice->update([
                'status' => $status,
                'paid_at' => $request->payment_date,
                'payment_remarks' => $request->remarks,
            ]);
        }

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
            'invoice_number' => $invoiceNumber,
            'student_id' => $installment->student_id,
            'bank_id' => null,
            'student_fee_submission_id' => $installment->id, // ← Changed
            'amount' => $installment->amount,
            'discount_amount' => 0,
            'due_date' => $installment->due_date,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Download challan
     */
    public function downloadChallan($id)
    {
        $installment = StudentFeeSubmission::with(['student', 'invoices']) // ← Changed
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
     * Upload payment proof
     */
    public function uploadProof(Request $request, $id)
    {
        $installment = StudentFeeSubmission::findOrFail($id); // ← Changed

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $invoice = $installment->invoices()->first();
        if ($invoice) {
            $invoice->update(['payment_proof_file' => $path]);
        }

        return back()->with('success', 'Payment proof uploaded successfully.');
    }

    /**
     * Approve payment
     */
    public function approvePayment($id)
    {
        $installment = StudentFeeSubmission::findOrFail($id); // ← Changed

        $installment->update([
            'status' => 'paid',
            'paid_amount' => $installment->amount,
            'payment_date' => now(),
        ]);

        $invoice = $installment->invoices()->first();
        if ($invoice) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'approved_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Payment approved successfully.');
    }
}