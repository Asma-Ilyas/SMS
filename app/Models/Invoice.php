<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'student_id', 'bank_id', 'student_fee_submission_id',
        'payment_method_id', 'amount', 'discount_amount', 'net_amount',
        'due_date', 'status', 'challan_file', 'payment_proof_file',
        'payment_remarks', 'paid_at', 'approved_by', 'discount_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function installment()
    {
        return $this->belongsTo(StudentFeeSubmission::class, 'student_fee_submission_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public static function generateInvoiceNumber()
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
    }

    public function generateChallan()
    {
        $this->load(['student', 'installment.feeType', 'paymentMethod']);
        $allBanks = Bank::where('is_active', true)->get();
        $mobileWallets = PaymentMethod::where('type', 'mobile_wallet')->where('is_active', true)->get();

        // Bug fix: was referencing an undefined $invoice variable — the view
        // needs $this (the invoice currently being generated), not a variable
        // that was never defined anywhere in this method.
        $invoice = $this;

        $pdf = Pdf::loadView('admin.fees.invoices.voucher', compact('invoice', 'allBanks', 'mobileWallets'));
        $path = 'invoices/voucher_' . $this->invoice_number . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        $this->challan_file = $path;
        $this->save();
        return Storage::disk('public')->path($path);
    }

    public function markAsPaid($paymentProofPath = null, $remarks = null, $userId = null)
    {
        $this->status = 'paid';
        $this->paid_at = now();
        if ($paymentProofPath) $this->payment_proof_file = $paymentProofPath;
        if ($remarks) $this->payment_remarks = $remarks;
        if ($userId) $this->approved_by = $userId;
        $this->save();

        if ($this->installment) {
            $this->installment->markAsPaid($this->paid_at, $this->invoice_number);
        }
    }

    public function applyDiscount($discountId, $discountAmount)
    {
        $this->discount_id = $discountId;
        $this->discount_amount = $discountAmount;
        $this->net_amount = max(0, $this->amount - $discountAmount);
        $this->save();
    }
}