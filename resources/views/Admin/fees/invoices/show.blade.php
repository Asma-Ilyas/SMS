@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
@php
    $studentName = trim(($invoice->student?->first_name ?? '') . ' ' . ($invoice->student?->last_name ?? '')) ?: 'N/A';
    $awaitingApproval = $invoice->payment_proof_file && in_array($invoice->status, ['pending', 'overdue'], true);
    $proofUrl = $invoice->payment_proof_file ? asset('storage/' . $invoice->payment_proof_file) : null;
    $isImage = $proofUrl && preg_match('/\.(jpg|jpeg|png)$/i', $invoice->payment_proof_file);
@endphp

<div class="max-w-3xl mx-auto px-4 py-6">
    <a href="{{ route('admin.invoices.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to invoices</a>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mt-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mt-4">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="bg-yellow-100 text-yellow-800 p-3 rounded mt-4">{{ session('warning') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mt-4">
            <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Invoice details --}}
    <div class="bg-white p-6 rounded-xl shadow mt-4">
        <div class="flex flex-wrap justify-between items-start gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Invoice #{{ $invoice->invoice_number }}</h2>
                <p class="text-gray-500">{{ $studentName }}
                    @if($invoice->student?->admission_number) &middot; {{ $invoice->student->admission_number }} @endif
                </p>
            </div>

            @if($invoice->status === 'paid')
                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Paid</span>
            @elseif($invoice->status === 'overdue')
                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">Overdue</span>
            @elseif($invoice->status === 'cancelled')
                <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">Cancelled</span>
            @else
                <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                    {{ $awaitingApproval ? 'Proof uploaded, awaiting approval' : 'Pending' }}
                </span>
            @endif
        </div>

        <dl class="grid grid-cols-2 gap-4 mt-5 text-sm">
            <div><dt class="text-gray-500">Amount</dt><dd class="font-semibold">Rs. {{ number_format($invoice->amount, 2) }}</dd></div>
            <div><dt class="text-gray-500">Due date</dt><dd class="font-semibold">{{ $invoice->due_date?->format('d-m-Y') }}</dd></div>
            @if($invoice->paid_at)
                <div><dt class="text-gray-500">Paid on</dt><dd class="font-semibold">{{ \Carbon\Carbon::parse($invoice->paid_at)->format('d-m-Y') }}</dd></div>
            @endif
        </dl>

        @if($invoice->challan_file)
            <a href="{{ route('admin.invoices.download-challan', $invoice) }}"
               class="inline-block mt-5 bg-gray-600 text-white px-3 py-1.5 rounded hover:bg-gray-700">Download Challan</a>
        @endif
    </div>

    {{-- Payment proof: review, approve or reject --}}
    @if($proofUrl)
    <div class="bg-white p-6 rounded-xl shadow mt-6 {{ $awaitingApproval ? 'border-2 border-yellow-300' : '' }}">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Payment proof</h3>

        @if($isImage)
            <a href="{{ $proofUrl }}" target="_blank">
                <img src="{{ $proofUrl }}" alt="Payment proof" class="max-h-96 rounded border mb-3">
            </a>
        @endif
        <a href="{{ $proofUrl }}" target="_blank" class="text-blue-600 underline">Open uploaded proof</a>

        @if($invoice->payment_remarks)
            <p class="text-sm text-gray-600 mt-3">Student's note: {{ $invoice->payment_remarks }}</p>
        @endif

        @if($awaitingApproval)
            <div class="flex flex-wrap items-start gap-4 mt-5 pt-5 border-t">
                <form method="POST" action="{{ route('admin.invoices.approve', $invoice) }}"
                      onsubmit="return confirm('Approve this payment and mark the invoice as paid?')">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Approve payment
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.invoices.reject', $invoice) }}" class="flex flex-wrap gap-2">
                    @csrf
                    <input type="text" name="reason" required maxlength="500" placeholder="Reason for rejecting"
                           class="border rounded px-3 py-2 w-64">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reject</button>
                </form>
            </div>
        @endif
    </div>
    @endif

    {{-- Optional: admin uploads a proof on behalf of a student (e.g. a paper receipt) --}}
    @if(in_array($invoice->status, ['pending', 'overdue'], true))
    <details class="bg-white p-6 rounded-xl shadow mt-6">
        <summary class="cursor-pointer font-medium text-gray-700">Upload a proof on behalf of the student</summary>
        <form method="POST" action="{{ route('admin.invoices.upload-proof', $invoice) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required class="block">
            <textarea name="remarks" placeholder="Remarks (optional)" rows="2" class="w-full border rounded px-3 py-2"></textarea>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
        </form>
    </details>
    @endif
</div>
@endsection