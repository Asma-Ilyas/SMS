@extends('layouts.app')

@section('title', 'Installment Details')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Installment #{{ $installment->installment_number }}</h2>
            @if($installment->status != 'paid')
                <a href="{{ route('admin.fee-installments.pay-form', $installment->id) }}" class="bg-green-600 text-white px-4 py-2 rounded">Record Payment</a>
            @endif
        </div>

        <div class="border-t border-b py-4 space-y-2">
            <p><strong>Student:</strong> {{ $installment->student->full_name }} ({{ $installment->student->admission_number }})</p>
            <p><strong>Fee Type:</strong> {{ $installment->feeType->name }}</p>
            <p><strong>Period:</strong> {{ ucfirst($installment->feeType->period) }}</p>
            <p><strong>Total Amount:</strong> ₹{{ number_format($installment->amount, 2) }}</p>
            <p><strong>Paid Amount:</strong> ₹{{ number_format($installment->paid_amount, 2) }}</p>
            <p><strong>Remaining:</strong> ₹{{ number_format($installment->remaining, 2) }}</p>
            <p><strong>Due Date:</strong> {{ $installment->due_date->format('d-m-Y') }}</p>
            <p><strong>Status:</strong>
                @if($installment->status == 'paid')
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Paid</span>
                @elseif($installment->status == 'partial')
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Partial</span>
                @else
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Pending</span>
                @endif
            </p>
            @if($installment->payment_date)
                <p><strong>Last Payment Date:</strong> {{ $installment->payment_date->format('d-m-Y') }}</p>
            @endif
            @if($installment->receipt_number)
                <p><strong>Receipt No.:</strong> {{ $installment->receipt_number }}</p>
            @endif
        </div>

        {{-- Voucher / Invoice Section --}}
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <h3 class="font-bold text-lg mb-3">📄 Fee Voucher</h3>
            @if($installment->invoice)
                <p><strong>Voucher #:</strong> {{ $installment->invoice->invoice_number }}</p>
                <p><strong>Payment Method:</strong> {{ $installment->invoice->paymentMethod->name ?? 'N/A' }}</p>
                @if($installment->invoice->discount_amount > 0)
                    <p><strong>Discount Applied:</strong> ₹{{ number_format($installment->invoice->discount_amount, 2) }}</p>
                @endif
                <p><strong>Voucher Status:</strong> {{ ucfirst($installment->invoice->status) }}</p>
                <div class="flex space-x-2 mt-2">
                    <a href="{{ route('admin.fee-installments.download-challan', $installment) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">📥 Download Voucher (3 Copies)</a>
                </div>

                @if($installment->status != 'paid')
                    <hr class="my-3">
                    <h4 class="font-semibold">📎 Upload Payment Proof</h4>
                    <form method="POST" action="{{ route('admin.fee-installments.upload-proof', $installment) }}" enctype="multipart/form-data" class="mt-2">
                        @csrf
                        <div class="flex flex-col space-y-2">
                            <input type="file" name="payment_proof" accept="image/*,pdf" required>
                            <textarea name="remarks" rows="2" class="border rounded px-2 py-1" placeholder="Transaction remarks (e.g., Easypaisa ref no, bank reference)"></textarea>
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm self-start">Upload Proof</button>
                        </div>
                    </form>
                @endif

                @if($installment->invoice->payment_proof_file && $installment->status != 'paid' && auth()->user()->is_admin)
                    <hr class="my-3">
                    <form method="POST" action="{{ route('admin.fee-installments.approve', $installment) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">✅ Approve Payment & Mark as Paid</button>
                    </form>
                @endif
            @else
                <p class="text-gray-600">No voucher generated yet. Select a payment method to generate a 3‑copy voucher.</p>
                <form method="POST" action="{{ route('admin.fee-installments.generate-invoice', $installment) }}" class="mt-2">
                    @csrf
                    <div class="flex items-center space-x-2">
                        <select name="payment_method_id" class="border rounded px-2 py-1" required>
                            <option value="">Select Payment Method</option>
                            @foreach(\App\Models\PaymentMethod::where('is_active', true)->get() as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} {{ $pm->type == 'mobile_wallet' ? "({$pm->account_number})" : '' }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-purple-600 text-white px-3 py-1 rounded">📄 Generate Voucher</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="flex justify-end space-x-2 mt-6">
            <a href="{{ route('admin.fee-installments.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
            @if($installment->status != 'paid')
                <form action="{{ route('admin.fee-installments.destroy', $installment) }}" method="POST" onsubmit="return confirm('Delete this installment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection