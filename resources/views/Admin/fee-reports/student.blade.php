{{-- resources/views/admin/fee-reports/student.blade.php --}}

@extends('layouts.app')

@section('title', 'Student Fee Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">👤 Student Fee Details</h1>
                    <p class="text-gray-500 mt-1">{{ $student->full_name }} - {{ $student->admission_number }}</p>
                </div>
                <a href="{{ route('admin.fee-reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <div class="text-xl font-bold text-gray-800">₹{{ number_format($summary['total_due'], 2) }}</div>
                <div class="text-sm text-gray-500">Total Due</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border border-green-200">
                <div class="text-xl font-bold text-green-600">₹{{ number_format($summary['total_paid'], 2) }}</div>
                <div class="text-sm text-green-600">✅ Paid</div>
            </div>
            <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border border-yellow-200">
                <div class="text-xl font-bold text-yellow-600">₹{{ number_format($summary['outstanding'], 2) }}</div>
                <div class="text-sm text-yellow-600">⏳ Outstanding</div>
            </div>
            <div class="bg-blue-50 rounded-xl shadow p-4 text-center border border-blue-200">
                <div class="text-xl font-bold text-blue-600">{{ $summary['total_installments'] }}</div>
                <div class="text-sm text-blue-600">📋 Installments</div>
            </div>
        </div>

        <!-- Installments Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-bold text-gray-800">📋 Installment History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Fee Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Installment</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Paid</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Due Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($installments as $item)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-sm">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 text-sm">{{ $item->feeSubmissionType->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm">{{ $item->installment_number }}</td>
                            <td class="px-4 py-2 text-sm font-semibold">₹{{ number_format($item->amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-green-600">₹{{ number_format($item->paid_amount, 2) }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $statusColors = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'partial' => 'bg-blue-100 text-blue-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                    $color = $statusColors[$item->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm {{ $item->due_date && $item->due_date->isPast() && $item->status != 'paid' ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $item->due_date ? $item->due_date->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-500">
                                {{ $item->payment_date ? $item->payment_date->format('d M Y') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No installments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection