<?php
// app/Http/Controllers/Admin/FeeInstallmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FeeSubmissionType;
use App\Models\StudentFeeInstallment;
use App\Models\Bank;
use App\Models\PaymentMethod;
use App\Models\Invoice;
use App\Models\User;
use App\Models\StudentDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PaymentProofUploaded;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeInstallmentController extends Controller
{
    /**
     * List all installments
     */
    public function index()
    {
        $installments = StudentFeeInstallment::with(['student', 'feeType', 'invoice'])
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
     * Store a new installment plan (2+ installments)
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'              => 'required|exists:students,id',
            'fee_submission_type_id'  => 'required|exists:fee_submission_types,id',
            'total_amount'            => 'required|numeric|min:0',
            'installment_count'       => 'required|integer|min:2|max:12',
            'due_dates'               => 'required|array|min:2',
            'due_dates.*'             => 'required|date',
        ]);

        // Delete any existing installments for same student+feetype
        StudentFeeInstallment::where('student_id', $request->student_id)
            ->where('fee_submission_type_id', $request->fee_submission_type_id)
            ->delete();

        $installmentAmount = round($request->total_amount / $request->installment_count, 2);
        $lastAdjustment = $request->total_amount - ($installmentAmount * ($request->installment_count - 1));

        for ($i = 1; $i <= $request->installment_count; $i++) {
            $amount = ($i == $request->installment_count) ? $lastAdjustment : $installmentAmount;
            StudentFeeInstallment::create([
                'student_id'               => $request->student_id,
                'fee_submission_type_id'   => $request->fee_submission_type_id,
                'installment_number'       => $i,
                'amount'                   => $amount,
                'due_date'                 => $request->due_dates[$i-1],
                'status'                   => 'pending',
                'paid_amount'              => 0,
            ]);
        }

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment plan created successfully.');
    }

    /**
     * Show details of a single installment
     */
    public function show($id)
    {
        $installment = StudentFeeInstallment::with(['student', 'feeType', 'invoice.paymentMethod'])->findOrFail($id);
        return view('admin.fees.installments.show', compact('installment'));
    }

    /**
     * Delete an installment (only if not paid)
     */
    public function destroy(StudentFeeInstallment $installment)
    {
        if ($installment->status == 'paid') {
            return back()->with('error', 'Cannot delete a paid installment.');
        }
        if ($installment->invoice) {
            $installment->invoice->delete();
        }
        $installment->delete();
        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Installment deleted.');
    }

    /**
     * Show form to record a payment for an installment
     */
    public function payForm(StudentFeeInstallment $installment)
    {
        if ($installment->status == 'paid') {
            return redirect()->route('admin.fee-installments.index')
                ->with('error', 'This installment is already fully paid.');
        }
        return view('admin.fees.installments.pay', compact('installment'));
    }

    /**
     * Process payment for an installment
     */
    public function pay(Request $request, StudentFeeInstallment $installment)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . $installment->remaining,
            'payment_date'   => 'required|date',
            'receipt_number' => 'nullable|string|max:100',
            'remarks'        => 'nullable|string',
        ]);

        $installment->recordPartialPayment(
            $request->amount,
            $request->payment_date,
            $request->receipt_number
        );
        $installment->remarks = $request->remarks;
        $installment->save();

        if ($installment->invoice && $installment->status == 'paid') {
            $installment->invoice->markAsPaid(
                $installment->invoice->payment_proof_file,
                $installment->invoice->payment_remarks,
                auth()->id()
            );
        }

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Generate a voucher (challan) for a specific installment
     */
    public function generateInvoice(Request $request, StudentFeeInstallment $installment)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        if ($installment->invoice) {
            return redirect()->route('admin.fee-installments.show', $installment)
                ->with('error', 'An invoice already exists for this installment.');
        }

        $paymentMethod = PaymentMethod::with('bank')->findOrFail($request->payment_method_id);

        // Fetch all active banks and mobile wallets for the voucher
        $allBanks = Bank::where('is_active', true)->get();
        $mobileWallets = PaymentMethod::where('type', 'mobile_wallet')->where('is_active', true)->get();

        // Apply discount logic
        $originalAmount = $installment->remaining;
        $finalAmount = $originalAmount;
        $discountId = null;
        $discountAmount = 0;

        $studentDiscounts = StudentDiscount::where('student_id', $installment->student_id)
            ->where(function ($q) use ($installment) {
                $q->where('fee_submission_type_id', $installment->fee_submission_type_id)
                  ->orWhereNull('fee_submission_type_id');
            })
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->with('discount')
            ->get();

        if ($studentDiscounts->count()) {
            $bestDiscount = null;
            $bestDiscountAmount = 0;
            foreach ($studentDiscounts as $sd) {
                $discountValue = $sd->discount->calculate($originalAmount);
                if ($discountValue > $bestDiscountAmount) {
                    $bestDiscountAmount = $discountValue;
                    $bestDiscount = $sd->discount;
                }
            }
            if ($bestDiscount) {
                $discountAmount = $bestDiscountAmount;
                $finalAmount = $originalAmount - $discountAmount;
                $discountId = $bestDiscount->id;
            }
        }

        $invoice = Invoice::create([
            'invoice_number'              => Invoice::generateInvoiceNumber(),
            'student_id'                  => $installment->student_id,
            'student_fee_installment_id'  => $installment->id,
            'payment_method_id'           => $paymentMethod->id,
            'amount'                      => $finalAmount,
            'due_date'                    => $installment->due_date,
            'status'                      => 'pending',
            'applied_discount_id'         => $discountId,
            'discount_amount'             => $discountAmount,
            'original_amount'             => $originalAmount,
            'discount_notes'              => $discountId ? 'Discount applied' : null,
        ]);

        // Generate voucher PDF
        $pdf = Pdf::loadView('admin.fees.invoices.voucher', compact('invoice', 'paymentMethod', 'allBanks', 'mobileWallets'));
        $path = 'invoices/voucher_' . $invoice->invoice_number . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        $invoice->challan_file = $path;
        $invoice->save();

        return redirect()->route('admin.fee-installments.show', $installment)
            ->with('success', 'Voucher generated.');
    }

    /**
     * Download the voucher PDF
     */
    public function downloadChallan(StudentFeeInstallment $installment)
    {
        if (!$installment->invoice || !$installment->invoice->challan_file) {
            return redirect()->back()->with('error', 'No voucher found for this installment.');
        }
        $file = Storage::disk('public')->path($installment->invoice->challan_file);
        return response()->download($file);
    }

    /**
     * Upload payment proof
     */
    public function uploadProof(Request $request, StudentFeeInstallment $installment)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'remarks'       => 'nullable|string',
        ]);

        if (!$installment->invoice) {
            return back()->with('error', 'No invoice associated. Please generate an invoice first.');
        }

        $invoice = $installment->invoice;
        $path = $request->file('payment_proof')->store('invoices/proofs', 'public');
        $invoice->payment_proof_file = $path;
        $invoice->payment_remarks = $request->remarks;
        $invoice->save();

        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new PaymentProofUploaded($invoice));
        }

        return redirect()->route('admin.fee-installments.show', $installment)
            ->with('success', 'Payment proof uploaded. Admin will review and approve.');
    }

    /**
     * Admin approves payment proof, marks installment as paid
     */
    public function approvePayment(StudentFeeInstallment $installment)
    {
        if (!$installment->invoice) {
            return back()->with('error', 'No invoice found for this installment.');
        }
        $invoice = $installment->invoice;
        if ($invoice->status == 'paid') {
            return back()->with('error', 'This payment is already approved.');
        }
        if (!$invoice->payment_proof_file) {
            return back()->with('error', 'No payment proof uploaded yet.');
        }

        $invoice->markAsPaid($invoice->payment_proof_file, $invoice->payment_remarks, auth()->id());
        if ($installment->status != 'paid') {
            $installment->markAsPaid(now(), $invoice->invoice_number);
        }

        return redirect()->route('admin.fee-installments.index')
            ->with('success', 'Payment approved. Installment marked as paid.');
    }
}