@extends('layouts.app')
@section('title', 'Fees & Invoices')
@section('content')
@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—';
    $money = fn($v) => 'Rs. '.number_format((float) $v, 0);
@endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'Fees & Invoices', 'subtitle' => 'Your invoices, installments and discounts'])

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">Total Invoiced</div><div class="text-2xl font-bold text-gray-800">{{ $money($totals['invoiced']) }}</div></div>
        <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">Paid</div><div class="text-2xl font-bold text-green-600">{{ $money($totals['paid']) }}</div></div>
        <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">Outstanding</div><div class="text-2xl font-bold {{ $totals['due'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $money($totals['due']) }}</div></div>
    </div>

    {{-- Invoices --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Invoices</div>
        @if($invoices->isEmpty())
            <div class="p-6 text-sm text-gray-500">No invoices yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3">Invoice #</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Discount</th><th class="px-4 py-3">Net</th><th class="px-4 py-3">Due</th><th class="px-4 py-3">Status</th><th></th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($invoices as $i)
                        @php $st = ($i->status === 'pending' && \Carbon\Carbon::parse($i->due_date)->isPast()) ? 'overdue' : $i->status; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $i->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $money($i->amount) }}</td>
                            <td class="px-4 py-3">{{ $money($i->discount_amount) }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $money($i->net_amount) }}</td>
                            <td class="px-4 py-3">{{ $fmt($i->due_date) }}</td>
                            <td class="px-4 py-3">@include('student.partials.badge', ['status' => $st])</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('student.fees.invoice', $i->id) }}" class="text-indigo-600 hover:underline">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Installments --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Fee Installments</div>
        @if($installments->isEmpty())
            <div class="p-6 text-sm text-gray-500">No fee installments assigned.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3">Fee</th><th class="px-4 py-3">Installment</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Paid</th><th class="px-4 py-3">Due</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Receipt</th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($installments as $f)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $f->type_name }} <span class="text-xs text-gray-400">{{ ucfirst(str_replace('_',' ',$f->period)) }}</span></td>
                            <td class="px-4 py-3">#{{ $f->installment_number }}</td>
                            <td class="px-4 py-3">{{ $money($f->amount) }}</td>
                            <td class="px-4 py-3">{{ $money($f->paid_amount) }}</td>
                            <td class="px-4 py-3">{{ $fmt($f->due_date) }}</td>
                            <td class="px-4 py-3">@include('student.partials.badge', ['status' => $f->status])</td>
                            <td class="px-4 py-3 text-gray-500">{{ $f->receipt_number ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($discounts->isNotEmpty())
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Assigned Discounts</h3>
            <ul class="space-y-2 text-sm">
                @foreach($discounts as $d)
                    <li class="flex justify-between border-b last:border-0 pb-2">
                        <span>{{ $d->name }}</span>
                        <span class="text-gray-600">{{ $d->type === 'percentage' ? rtrim(rtrim($d->value,'0'),'.').'%' : $money($d->value) }}
                            @if($d->valid_until) · until {{ $fmt($d->valid_until) }} @endif</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
