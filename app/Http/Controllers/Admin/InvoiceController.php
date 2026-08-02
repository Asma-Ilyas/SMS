<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Bank;
use App\Models\StudentFeeInstallment;
use App\Models\ClassSection;           // ✅ replaced Classes with ClassSection
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['student', 'installment'])->latest()->paginate(20);
        return view('admin.fees.invoices.index', compact('invoices'));
    }

    /**
     * Show create form – class sections → students → installments → BANKS only
     */
    public function create()
    {
        // ✅ Fetch class sections with related class (for display)
        $classSections = ClassSection::with('class.grade')->orderBy('class_id')->get();
        // Only banks (type = 'bank'), active – but we don't have type field in banks, keep as is
        $banks = Bank::where('is_active', true)->get();
        return view('admin.fees.invoices.create', compact('classSections', 'banks'));
    }

    /**
     * ✅ AJAX: Get students by class_section_id (replaces old getStudentsByClass)
     */
    public function getStudentsByClassSection($classSectionId)
    {
        $students = Student::where('class_section_id', $classSectionId)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'roll_number']);

        $formatted = $students->map(function ($student) {
            return [
                'id'          => $student->id,
                'name'        => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
                'roll_number' => $student->roll_number,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * AJAX: Get installments (pending/partial) for a student (no change needed)
     */
    public function getInstallmentsByStudent($studentId)
    {
        $installments = StudentFeeInstallment::where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial'])
            ->with('feeType')
            ->get();

        $formatted = $installments->map(function ($inst) {
            return [
                'id'        => $inst->id,
                'fee_type'  => $inst->feeType->name ?? 'Fee',
                'due_date'  => $inst->due_date ? $inst->due_date->format('Y-m-d') : null,
                'remaining' => number_format($inst->remaining, 2),
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Store invoice – uses bank ID (payment_method_id column stores bank id)
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'                => 'required|exists:students,id',
            'student_fee_installment_id'=> 'required|exists:student_fee_installments,id',
            'bank_id'                   => 'required|exists:banks,id',
            'amount'                    => 'required|numeric|min:0',
            'due_date'                  => 'required|date',
            'late_fee'                  => 'nullable|numeric|min:0',
            'discount'                  => 'nullable|numeric|min:0',
            'other_charge_desc'         => 'nullable|string|max:255',
            'other_charge_amount'       => 'nullable|numeric|min:0',
        ]);

        $metadata = [
            'late_fee'          => $request->late_fee ?? 0,
            'discount'          => $request->discount ?? 0,
            'other_charge_desc' => $request->other_charge_desc,
            'other_charge_amount' => $request->other_charge_amount ?? 0,
        ];

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'student_id'     => $request->student_id,
            'student_fee_installment_id' => $request->student_fee_installment_id,
            'bank_id'        => $request->bank_id,
            'amount'         => $request->amount,
            'due_date'       => $request->due_date,
            'status'         => 'pending',
            'metadata'       => json_encode($metadata),
        ]);

        try {
            $invoice->generateChallan(); // PDF voucher
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('warning', 'Invoice created but PDF failed. You can retry later.');
        }

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice and voucher created successfully.');
    }

    public function show(Invoice $invoice)
    {
        return view('admin.fees.invoices.show', compact('invoice'));
    }

    public function downloadChallan(Invoice $invoice)
    {
        if (!$invoice->challan_file || !Storage::disk('public')->exists($invoice->challan_file)) {
            $invoice->generateChallan();
        }
        return response()->download(Storage::disk('public')->path($invoice->challan_file));
    }

    public function uploadPaymentProof(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,png,pdf|max:2048',
            'remarks'       => 'nullable|string',
        ]);

        $path = $request->file('payment_proof')->store('invoices/proofs', 'public');
        $invoice->payment_proof_file = $path;
        $invoice->payment_remarks = $request->remarks;
        $invoice->save();

        $admin = \App\Models\User::where('is_admin', true)->first();
        if ($admin) $admin->notify(new \App\Notifications\PaymentProofUploaded($invoice));

        return redirect()->back()->with('success', 'Payment proof uploaded. Admin will approve.');
    }

    public function approvePayment(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Already paid.');
        }
        $invoice->markAsPaid($invoice->payment_proof_file, $invoice->payment_remarks, auth()->id());
        return redirect()->route('admin.invoices.index')->with('success', 'Payment approved.');
    }
}