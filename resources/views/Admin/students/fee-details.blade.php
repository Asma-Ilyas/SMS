{{-- resources/views/admin/students/fee-details.blade.php --}}

@extends('layouts.app')

@section('title', 'Fee Details - ' . ($student->first_name ?? 'N/A') . ' ' . ($student->last_name ?? ''))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">💰 Fee Details</h1>
                    <p class="text-gray-500 mt-0.5">{{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }} - {{ $student->admission_number ?? 'N/A' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.students.show', $student->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        👤 Back to Profile
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back to Students
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        🖨 Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Info Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Student Name</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->first_name ?? 'N/A' }} {{ $student->last_name ?? '' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Class</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->classSection->full_name ?? 'N/A' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Roll No</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->roll_number ?? 'N/A' }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">
                <div class="text-xs text-gray-400 uppercase tracking-wider">Admission No</div>
                <div class="font-bold text-gray-800 mt-1">{{ $student->admission_number ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Fee Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center hover:shadow-lg transition">
                <div class="text-2xl font-bold text-gray-800">₹{{ number_format($feeSubmissions->sum('amount'), 2) }}</div>
                <div class="text-xs text-gray-500">Total Fee</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border border-green-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-green-600">₹{{ number_format($totalPaid, 2) }}</div>
                <div class="text-xs text-green-600">✅ Paid</div>
            </div>
            <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border border-yellow-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-yellow-600">₹{{ number_format($totalDue, 2) }}</div>
                <div class="text-xs text-yellow-600">⏳ Pending</div>
            </div>
            <div class="bg-purple-50 rounded-xl shadow p-4 text-center border border-purple-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-purple-600">{{ $feeSubmissions->where('status', 'paid')->count() }} / {{ $feeSubmissions->count() }}</div>
                <div class="text-xs text-purple-600">Installments Paid</div>
            </div>
        </div>

        <!-- Fee Installments Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📋</span> Fee Installments
                </h2>
                <span class="text-sm text-gray-500">{{ $feeSubmissions->count() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fee Type</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Installment</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Paid</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Remaining</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Due Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($feeSubmissions as $fee)
                            @php
                                $remaining = $fee->amount - $fee->paid_amount;
                                $isOverdue = $fee->due_date && $fee->due_date < now() && $fee->status != 'paid';
                                $progress = $fee->amount > 0 ? round(($fee->paid_amount / $fee->amount) * 100, 2) : 0;
                            @endphp
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $fee->feeSubmissionType->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ $fee->installment_number ?? 1 }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-800">₹{{ number_format($fee->amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-green-600 font-semibold">₹{{ number_format($fee->paid_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm {{ $remaining > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                    ₹{{ number_format($remaining, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($fee->status == 'paid')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Paid</span>
                                    @elseif($fee->status == 'partial')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">🔶 Partial</span>
                                    @elseif($isOverdue)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">🔴 Overdue</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $fee->due_date ? $fee->due_date->format('d M Y') : 'N/A' }}
                                    @if($isOverdue)
                                        <span class="text-xs text-red-500 ml-1">⚠️</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500">
                                    {{ $fee->payment_date ? $fee->payment_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $fee->receipt_number ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2">No fee records found for this student</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices -->
        @if($invoices->count() > 0)
        <div class="mt-6 bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📄</span> Invoices
                </h2>
                <span class="text-sm text-gray-500">{{ $invoices->count() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice #</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Paid</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Due Date</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $invoice->invoice_number ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold">₹{{ number_format($invoice->amount ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-green-600 font-semibold">₹{{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($invoice->status == 'paid')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Paid</span>
                                    @elseif($invoice->status == 'overdue')
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">🔴 Overdue</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ $invoice->created_at ? $invoice->created_at->format('d M Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $invoice->bank->name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Fee Summary Chart -->
        <div class="mt-6 bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📊</span> Fee Summary
                </h2>
            </div>
            <div class="p-6">
                @php
                    $totalFee = $feeSubmissions->sum('amount');
                    $totalPaid = $feeSubmissions->where('status', 'paid')->sum('paid_amount');
                    $totalPending = $feeSubmissions->whereIn('status', ['pending', 'partial'])->sum('amount');
                    $paidPercentage = $totalFee > 0 ? round(($totalPaid / $totalFee) * 100, 2) : 0;
                @endphp
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">Fee Collection Status</span>
                            <span class="font-bold text-indigo-600">{{ $paidPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="h-4 rounded-full bg-gradient-to-r from-green-500 to-indigo-600 transition-all duration-1000" style="width: {{ $paidPercentage }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Paid: ₹{{ number_format($totalPaid, 2) }}</span>
                            <span>Pending: ₹{{ number_format($totalPending, 2) }}</span>
                            <span>Total: ₹{{ number_format($totalFee, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 grid grid-cols-3 gap-2 text-center">
                        <div class="bg-green-50 rounded-lg p-2 border border-green-200">
                            <div class="text-lg font-bold text-green-600">{{ $feeSubmissions->where('status', 'paid')->count() }}</div>
                            <div class="text-xs text-green-700">✅ Paid</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-2 border border-yellow-200">
                            <div class="text-lg font-bold text-yellow-600">{{ $feeSubmissions->where('status', 'pending')->count() }}</div>
                            <div class="text-xs text-yellow-700">⏳ Pending</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-2 border border-blue-200">
                            <div class="text-lg font-bold text-blue-600">{{ $feeSubmissions->where('status', 'partial')->count() }}</div>
                            <div class="text-xs text-blue-700">🔶 Partial</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    .container { max-width: 100% !important; padding: 0 !important; }
    .shadow-xl { box-shadow: none !important; }
    .rounded-2xl { border-radius: 0 !important; }
    .bg-gradient-to-br { background: white !important; }
    .border { border-color: #e2e8f0 !important; }
}
</style>
@endpush
@endsection