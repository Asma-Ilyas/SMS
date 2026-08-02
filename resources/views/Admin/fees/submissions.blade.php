{{-- resources/views/Admin/fees/submissions.blade.php --}}

@extends('layouts.app')

@section('title', 'Fee Submissions')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        💰 Fee Submissions
                    </h1>
                    <p class="text-gray-500 mt-1">Manage student fee submissions and payments</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.fee-submissions.create') }}" 
                       class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Submission
                    </a>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-5 py-2.5 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 hover:shadow-2xl transition-all duration-300">
            <form method="GET" action="{{ route('admin.fee-submissions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500" 
                           placeholder="Search by name...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.fee-submissions.index') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-all duration-300">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-gray-800">{{ $submissions->total() }}</div>
                <div class="text-sm text-gray-500">Total Submissions</div>
            </div>
            <div class="bg-green-50 rounded-2xl shadow-lg p-4 text-center border-2 border-green-200 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-green-600">
                    {{ $submissions->where('status', 'paid')->count() }}
                </div>
                <div class="text-sm text-green-700">✅ Paid</div>
            </div>
            <div class="bg-yellow-50 rounded-2xl shadow-lg p-4 text-center border-2 border-yellow-200 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-yellow-600">
                    {{ $submissions->where('status', 'pending')->count() }}
                </div>
                <div class="text-sm text-yellow-700">⏳ Pending</div>
            </div>
            <div class="bg-red-50 rounded-2xl shadow-lg p-4 text-center border-2 border-red-200 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-red-600">
                    ₹{{ number_format($submissions->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-red-700">Total Amount</div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📋</span> Fee Submissions
                </h2>
                <span class="text-sm text-gray-500">{{ $submissions->total() }} records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fee Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Period</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receipt</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($submissions as $sub)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">
                                    @if($sub->student)
                                        {{ $sub->student->full_name ?? 'N/A' }}
                                    @else
                                        <span class="text-gray-400">Student not found</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400">
                                    @if($sub->student)
                                        {{ $sub->student->admission_number ?? 'N/A' }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400">
                                    @if($sub->student && $sub->student->classSection)
                                        {{ $sub->student->classSection->full_name ?? 'N/A' }}
                                    @else
                                        No Class
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($sub->feeType)
                                    <span class="font-medium">{{ $sub->feeType->name }}</span>
                                @elseif($sub->feeSubmissionType)
                                    <span class="font-medium">{{ $sub->feeSubmissionType->name }}</span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm capitalize">{{ $sub->period ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800">₹{{ number_format($sub->amount ?? 0, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $status = $sub->status ?? 'pending';
                                    $statusColors = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'partial' => 'bg-blue-100 text-blue-800',
                                        'overdue' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $sub->submission_date ? $sub->submission_date->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $sub->receipt_number ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.fee-submissions.show', $sub->id) }}" 
                                       class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition-all duration-200">
                                        👁️ View
                                    </a>
                                    <a href="{{ route('admin.fee-submissions.edit', $sub->id) }}" 
                                       class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-lg transition-all duration-200">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('admin.fee-submissions.destroy', $sub->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition-all duration-200" 
                                                onclick="return confirm('Are you sure you want to delete this submission?')">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2">No fee submissions found</p>
                                <p class="text-sm">Start by adding a new fee submission</p>
                                <a href="{{ route('admin.fee-submissions.create') }}" class="mt-4 inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                    + Add Submission
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $submissions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection