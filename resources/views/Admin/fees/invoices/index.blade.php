@extends('layouts.app')

@section('title', 'Invoices / Challans')

@section('content')
@php
    $awaitingCount = $awaitingCount ?? 0;
    $onAwaiting = request('filter') === 'awaiting';
@endphp

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Invoices / Challans</h1>
        <a href="{{ route('admin.invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Generate New Invoice
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-2 mb-4">
        <a href="{{ route('admin.invoices.index') }}"
           class="px-4 py-2 rounded {{ !$onAwaiting ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All invoices
        </a>
        <a href="{{ route('admin.invoices.index', ['filter' => 'awaiting']) }}"
           class="px-4 py-2 rounded {{ $onAwaiting ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Awaiting approval
            @if($awaitingCount > 0)
                <span class="ml-1 rounded-full bg-red-500 text-white text-xs px-2 py-0.5">{{ $awaitingCount }}</span>
            @endif
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount (Rs.)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                @php
                    $name = trim(($invoice->student?->first_name ?? '') . ' ' . ($invoice->student?->last_name ?? '')) ?: 'N/A';
                    $hasProof = $invoice->payment_proof_file && in_array($invoice->status, ['pending', 'overdue'], true);
                @endphp
                <tr class="{{ $hasProof ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-3">{{ $invoice->invoice_number }}</td>
                    <td class="px-6 py-3">{{ $name }}<br><small class="text-gray-500">{{ $invoice->student?->admission_number }}</small></td>
                    <td class="px-6 py-3">{{ number_format($invoice->amount, 2) }}</td>
                    <td class="px-6 py-3">{{ $invoice->due_date?->format('d-m-Y') }}</td>
                    <td class="px-6 py-3">{{ $invoice->bank->name ?? 'N/A' }}</td>
                    <td class="px-6 py-3">
                        @if($invoice->status == 'paid')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span>
                        @elseif($invoice->status == 'overdue')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Overdue</span>
                        @elseif($invoice->status == 'cancelled')
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Cancelled</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif

                        @if($hasProof)
                            <span class="block mt-1 text-xs font-medium text-yellow-800">Proof uploaded</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 space-x-2 whitespace-nowrap">
                        @if($hasProof)
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm">Review</a>
                        @else
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-blue-600 hover:underline">View</a>
                        @endif
                        @if($invoice->challan_file)
                            <a href="{{ route('admin.invoices.download-challan', $invoice) }}" class="text-green-600 hover:underline">Download Challan</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-gray-500">
                        {{ $onAwaiting ? 'No payments are waiting for approval.' : 'No invoices found.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection