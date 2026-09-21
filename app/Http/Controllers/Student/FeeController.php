<?php

namespace App\Http\Controllers\Student;

use App\Support\PortalAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class FeeController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();

        $invoices = DB::table('invoices')
            ->where('student_id', $s->id)
            ->orderByDesc('due_date')->get();

        $installments = DB::table('student_fee_submissions as f')
            ->join('fee_submission_types as t', 't.id', '=', 'f.fee_submission_type_id')
            ->where('f.student_id', $s->id)
            ->orderBy('f.due_date')
            ->select('f.*', 't.name as type_name', 't.period')->get();

        $discounts = DB::table('student_discounts as sd')
            ->join('discounts as d', 'd.id', '=', 'sd.discount_id')
            ->where('sd.student_id', $s->id)
            ->select('d.name', 'd.type', 'd.value', 'sd.valid_from', 'sd.valid_until')->get();

        $totals = [
            'invoiced' => $invoices->where('status', '!=', 'cancelled')->sum('net_amount'),
            'paid'     => $invoices->where('status', 'paid')->sum('net_amount'),
            'due'      => $invoices->whereIn('status', ['pending', 'overdue'])->sum('net_amount'),
        ];

        return view('student.fees.index', compact('s', 'invoices', 'installments', 'discounts', 'totals'));
    }

    public function showInvoice($invoice)
    {
        $s = $this->student();

        $invoice = DB::table('invoices as i')
            ->leftJoin('banks as b', 'b.id', '=', 'i.bank_id')
            ->leftJoin('student_fee_submissions as f', 'f.id', '=', 'i.student_fee_submission_id')
            ->leftJoin('fee_submission_types as t', 't.id', '=', 'f.fee_submission_type_id')
            ->leftJoin('discounts as d', 'd.id', '=', 'i.discount_id')
            ->where('i.id', $invoice)
            ->select('i.*', 'b.name as bank_name', 'b.branch_name', 'b.account_title', 'b.account_number', 'b.iban',
                     't.name as fee_type', 'f.installment_number', 'd.name as discount_name')
            ->first();

        $this->ensureOwner($invoice);

        $methods = Schema::hasTable('payment_methods')
            ? DB::table('payment_methods')->where('is_active', 1)->get()
            : collect();

        return view('student.fees.invoice', compact('s', 'invoice', 'methods'));
    }

    public function downloadChallan($invoice)
    {
        $this->student();
        $invoice = DB::table('invoices')->where('id', $invoice)->first();
        $this->ensureOwner($invoice);

        abort_if(!$invoice->challan_file || !Storage::disk('public')->exists($invoice->challan_file), 404, 'Challan not available yet.');

        return Storage::disk('public')->download($invoice->challan_file, 'challan-' . $invoice->invoice_number . '.' . pathinfo($invoice->challan_file, PATHINFO_EXTENSION));
    }

    public function uploadProof(Request $request, $invoice)
    {
        $s = $this->student();
        $invoice = DB::table('invoices')->where('id', $invoice)->first();
        $this->ensureOwner($invoice);

        abort_if(!in_array($invoice->status, ['pending', 'overdue'], true), 403, 'This invoice is not payable.');

        $request->validate([
            'payment_proof'   => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'payment_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        // Replacing an earlier proof: remove the old file so it doesn't pile up.
        if ($invoice->payment_proof_file && $invoice->payment_proof_file !== $path) {
            Storage::disk('public')->delete($invoice->payment_proof_file);
        }

        DB::table('invoices')->where('id', $invoice->id)->update([
            'payment_proof_file' => $path,
            'payment_remarks'    => $request->payment_remarks,
            'updated_at'         => now(),
        ]);

        // Tell every admin that a proof is waiting for approval.
        $studentName = trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? '')) ?: 'A student';

        PortalAlert::toAdmins(
            'Payment proof uploaded',
            $studentName . ' uploaded a payment proof for invoice ' . $invoice->invoice_number . '. It needs approval.',
            route('admin.invoices.show', $invoice->id),
            'fee',
            'info'
        );

        return back()->with('success', 'Payment proof uploaded. The office will verify and approve it.');
    }
}