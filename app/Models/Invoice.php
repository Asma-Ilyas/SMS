<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'student_id', 'bank_id', 'student_fee_installment_id',
        'payment_method_id', 'amount', 'due_date', 'status', 'challan_file',
        'payment_proof_file', 'payment_remarks', 'paid_at', 'approved_by',
        'discount_id', 'discount_amount', 'original_amount', 'discount_notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
    ];

    // Relationships
    public function student() { return $this->belongsTo(Student::class); }
    public function bank() { return $this->belongsTo(Bank::class); }
    public function installment() { return $this->belongsTo(StudentFeeInstallment::class, 'student_fee_installment_id'); }
    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function discount() { return $this->belongsTo(Discount::class); }

    // Generate unique invoice number
    public static function generateInvoiceNumber()
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
    }

    // Generate PDF voucher with ALL active banks
    public function generateChallan()
    {
        $this->load(['student', 'installment.feeType', 'paymentMethod']);
        $invoice = $this;
        $installment = $this->installment;
        $paymentMethod = $this->paymentMethod;

        // ✅ Get ALL active banks
        $allBanks = Bank::where('is_active', true)->get();
        // Get mobile wallets (optional)
        $mobileWallets = PaymentMethod::where('type', 'mobile_wallet')->where('is_active', true)->get();

        $pdf = Pdf::loadView('admin.fees.invoices.voucher', compact(
            'invoice', 'installment', 'paymentMethod', 'allBanks', 'mobileWallets'
        ));
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
        $this->original_amount = $this->amount;
        $this->amount = max(0, $this->amount - $discountAmount);
        $this->save();
    }
}