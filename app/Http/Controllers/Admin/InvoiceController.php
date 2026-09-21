<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Bank;
use App\Models\StudentFeeInstallment;
use App\Models\ClassSection;
use App\Support\PortalAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * List invoices. ?filter=awaiting shows only invoices that have a payment
     * proof uploaded and are not yet approved. ?status=paid|pending|overdue works too.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'installment'])->latest();

        if ($request->get('filter') === 'awaiting') {
            $query->whereNotNull('payment_proof_file')->whereIn('status', ['pending', 'overdue']);
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(20)->withQueryString();

        $awaitingCount = Invoice::whereNotNull('payment_proof_file')
            ->whereIn('status', ['pending', 'overdue'])
            ->count();

        return view('admin.fees.invoices.index', compact('invoices', 'awaitingCount'));
    }

    /**
     * Show create form: class sections -> students -> installments -> banks
     */
    public function create()
    {
        $classSections = ClassSection::with('class.grade')->orderBy('class_id')->get();
        $banks = Bank::where('is_active', true)->get();
        return view('admin.fees.invoices.create', compact('classSections', 'banks'));
    }

    /**
     * AJAX: students of one class section
     */
    public function getStudentsByClassSection($classSectionId)
    {
        $students = Student::where('class_section_id', $classSectionId)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'roll_number']);

        $formatted = $students->map(function ($student) {
            return [
                'id'          => $student->id,
                'name'        => trim($student->first_name . ' ' . $student->last_name),
                'roll_number' => $student->roll_number,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * AJAX: pending/partial installments for a student
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
     * Store invoice (uses bank ID) and tell the student a new invoice exists.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'                 => 'required|exists:students,id',
            'student_fee_installment_id' => 'required|exists:student_fee_installments,id',
            'bank_id'                    => 'required|exists:banks,id',
            'amount'                     => 'required|numeric|min:0',
            'due_date'                   => 'required|date',
            'late_fee'                   => 'nullable|numeric|min:0',
            'discount'                   => 'nullable|numeric|min:0',
            'other_charge_desc'          => 'nullable|string|max:255',
            'other_charge_amount'        => 'nullable|numeric|min:0',
        ]);

        $metadata = [
            'late_fee'            => $request->late_fee ?? 0,
            'discount'            => $request->discount ?? 0,
            'other_charge_desc'   => $request->other_charge_desc,
            'other_charge_amount' => $request->other_charge_amount ?? 0,
        ];

        $invoice = Invoice::create([
            'invoice_number'             => Invoice::generateInvoiceNumber(),
            'student_id'                 => $request->student_id,
            'student_fee_installment_id' => $request->student_fee_installment_id,
            'bank_id'                    => $request->bank_id,
            'amount'                     => $request->amount,
            'due_date'                   => $request->due_date,
            'status'                     => 'pending',
            'metadata'                   => json_encode($metadata),
        ]);

        // Notify the student before the PDF step, so a PDF failure never skips it.
        PortalAlert::toStudent(
            $invoice->student_id,
            'New fee invoice',
            'Invoice ' . $invoice->invoice_number . ' of Rs. ' . number_format((float) $invoice->amount, 2)
                . ' is due on ' . \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') . '.',
            PortalAlert::link('student.fees.index', '/student/fees'),
            'fee',
            'info'
        );

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

    /**
     * Payment proof uploaded: tell every admin.
     */
    public function uploadPaymentProof(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'remarks'       => 'nullable|string',
        ]);

        $path = $request->file('payment_proof')->store('invoices/proofs', 'public');
        $invoice->payment_proof_file = $path;
        $invoice->payment_remarks = $request->remarks;
        $invoice->save();

        $studentName = trim(($invoice->student?->first_name ?? '') . ' ' . ($invoice->student?->last_name ?? '')) ?: 'A student';

        PortalAlert::toAdmins(
            'Payment proof uploaded',
            $studentName . ' uploaded a payment proof for invoice ' . $invoice->invoice_number . '. It needs approval.',
            route('admin.invoices.show', $invoice),
            'fee',
            'info'
        );

        return redirect()->back()->with('success', 'Payment proof uploaded. Admin will approve.');
    }

    /**
     * Payment approved: tell the student.
     */
    public function approvePayment(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Already paid.');
        }

        $invoice->markAsPaid($invoice->payment_proof_file, $invoice->payment_remarks, auth()->id());

        PortalAlert::toStudent(
            $invoice->student_id,
            'Payment approved',
            'Your payment for invoice ' . $invoice->invoice_number . ' has been approved.',
            PortalAlert::link('student.fees.index', '/student/fees'),
            'fee',
            'success'
        );

        return redirect()->route('admin.invoices.index')->with('success', 'Payment approved.');
    }

    /**
     * Payment proof rejected: clear it, keep the invoice payable, tell the student why.
     * (invoices.status has no "rejected" value, so the invoice simply stays pending/overdue.)
     */
    public function rejectPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'This invoice is already paid.');
        }

        if ($invoice->payment_proof_file) {
            Storage::disk('public')->delete($invoice->payment_proof_file);
        }

        $invoice->payment_proof_file = null;
        $invoice->payment_remarks = null;
        $invoice->save();

        PortalAlert::toStudent(
            $invoice->student_id,
            'Payment proof rejected',
            'Your payment proof for invoice ' . $invoice->invoice_number . ' was rejected: ' . $request->reason
                . ' Please upload a correct proof.',
            PortalAlert::link('student.fees.index', '/student/fees'),
            'fee',
            'warning'
        );

        return redirect()->route('admin.invoices.index', ['filter' => 'awaiting'])
            ->with('success', 'Payment proof rejected and the student was notified.');
    }
}