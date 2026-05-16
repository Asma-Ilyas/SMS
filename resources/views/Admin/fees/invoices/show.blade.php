@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white p-6 rounded shadow">
        <h2>Invoice #{{ $invoice->invoice_number }}</h2>
        <p>Student: {{ $invoice->student->full_name }}</p>
        <p>Amount: ₹{{ number_format($invoice->amount,2) }}</p>
        <p>Due Date: {{ $invoice->due_date->format('d-m-Y') }}</p>
        <p>Status: <strong>{{ ucfirst($invoice->status) }}</strong></p>

        @if($invoice->challan_file)
            <a href="{{ route('admin.invoices.download-challan', $invoice) }}" class="bg-gray-600 text-white px-3 py-1 rounded">Download Challan</a>
        @endif

        @if($invoice->status == 'pending')
            <hr class="my-4">
            <h3>Student: Upload Payment Proof</h3>
            <form method="POST" action="{{ route('admin.invoices.upload-proof', $invoice) }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="payment_proof" accept="image/*,pdf" required>
                <textarea name="remarks" placeholder="Remarks" rows="2"></textarea>
                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Upload</button>
            </form>
        @endif

        @if($invoice->payment_proof_file && $invoice->status == 'pending' && auth()->user()->is_admin)
            <hr class="my-4">
            <form method="POST" action="{{ route('admin.invoices.approve', $invoice) }}">
                @csrf
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Approve Payment & Mark as Paid</button>
            </form>
        @endif
    </div>
</div>
@endsection