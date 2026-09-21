@extends('layouts.app')
@section('title', 'Invoice '.$invoice->invoice_number)
@section('content')
@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—';
    $money = fn($v) => 'Rs. '.number_format((float) $v, 0);
    $st = ($invoice->status === 'pending' && \Carbon\Carbon::parse($invoice->due_date)->isPast()) ? 'overdue' : $invoice->status;
    $payable = in_array($invoice->status, ['pending', 'overdue']);
@endphp
<div class="space-y-6 max-w-3xl">
    @include('student.partials.header', ['title' => 'Invoice '.$invoice->invoice_number, 'subtitle' => $s->first_name.' '.$s->last_name])

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm text-gray-500">Due date: <strong class="text-gray-800">{{ $fmt($invoice->due_date) }}</strong></div>
            @include('student.partials.badge', ['status' => $st])
        </div>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-xs uppercase text-gray-500">Fee</dt><dd>{{ $invoice->fee_type ?? '—' }} @if($invoice->installment_number) (Installment #{{ $invoice->installment_number }}) @endif</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Amount</dt><dd>{{ $money($invoice->amount) }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Discount</dt><dd>{{ $money($invoice->discount_amount) }} @if($invoice->discount_name) ({{ $invoice->discount_name }}) @endif</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Net payable</dt><dd class="text-lg font-bold text-indigo-600">{{ $money($invoice->net_amount) }}</dd></div>
            @if($invoice->paid_at)<div><dt class="text-xs uppercase text-gray-500">Paid on</dt><dd>{{ $fmt($invoice->paid_at) }}</dd></div>@endif
        </dl>

        @if($invoice->challan_file)
            <a href="{{ route('student.fees.challan', $invoice->id) }}" class="inline-block mt-5 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">⬇ Download Challan</a>
        @endif
    </div>

    @if($invoice->bank_name)
        <div class="bg-white rounded-xl border shadow-sm p-6 text-sm">
            <h3 class="font-semibold text-gray-800 mb-3">Bank Details</h3>
            <dl class="grid grid-cols-2 gap-3">
                <div><dt class="text-xs uppercase text-gray-500">Bank</dt><dd>{{ $invoice->bank_name }} – {{ $invoice->branch_name }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Account Title</dt><dd>{{ $invoice->account_title }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Account #</dt><dd>{{ $invoice->account_number }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">IBAN</dt><dd>{{ $invoice->iban ?: '—' }}</dd></div>
            </dl>
        </div>
    @endif

    @if($methods->isNotEmpty())
        <div class="bg-white rounded-xl border shadow-sm p-6 text-sm">
            <h3 class="font-semibold text-gray-800 mb-3">Other Payment Methods</h3>
            <ul class="space-y-2">
                @foreach($methods as $m)
                    <li><strong>{{ $m->name }}</strong> ({{ str_replace('_',' ',$m->type) }}) @if($m->account_number) – {{ $m->account_number }} @endif
                        @if($m->instructions)<div class="text-xs text-gray-500">{{ $m->instructions }}</div>@endif</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($payable)
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-1">Upload Payment Proof</h3>
            <p class="text-xs text-gray-500 mb-4">After paying, upload the receipt (JPG, PNG or PDF, max 4 MB). The office will verify it.</p>
            @if($invoice->payment_proof_file)
                <p class="text-xs text-green-700 mb-3">✔ A proof has already been uploaded. Uploading again will replace it.</p>
            @endif
            <form method="POST" action="{{ route('student.fees.proof', $invoice->id) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm">
                <textarea name="payment_remarks" rows="2" placeholder="Remarks (optional)" class="w-full rounded-lg border-gray-300 text-sm">{{ old('payment_remarks', $invoice->payment_remarks) }}</textarea>
                <button class="px-5 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Upload Proof</button>
            </form>
        </div>
    @endif

    <a href="{{ route('student.fees.index') }}" class="inline-block text-sm text-indigo-600 hover:underline">← Back to fees</a>
</div>
@endsection
