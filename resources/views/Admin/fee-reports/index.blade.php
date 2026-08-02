{{-- resources/views/admin/fee-reports/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Fee Reports')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        📊 Fee Reports
                    </h1>
                    <p class="text-gray-500 mt-1">View and analyze fee collection reports</p>
                </div>
                <div class="flex gap-3">
                  
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-5 py-2.5 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-5 text-center hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-gray-800">₹{{ number_format($totalDue, 2) }}</div>
                <div class="text-sm text-gray-500 mt-1">💰 Total Due</div>
            </div>
            <div class="bg-green-50 rounded-2xl shadow-lg p-5 text-center border-2 border-green-200 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-green-600">₹{{ number_format($totalPaid, 2) }}</div>
                <div class="text-sm text-green-700 mt-1">✅ Total Paid</div>
            </div>
            <div class="bg-yellow-50 rounded-2xl shadow-lg p-5 text-center border-2 border-yellow-200 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-yellow-600">₹{{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-sm text-yellow-700 mt-1">⏳ Outstanding</div>
            </div>
            <div class="bg-red-50 rounded-2xl shadow-lg p-5 text-center border-2 border-red-200 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="text-2xl font-bold text-red-600">₹{{ number_format($totalOverdue, 2) }}</div>
                <div class="text-sm text-red-700 mt-1">🔴 Overdue</div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center hover:shadow-xl transition-all duration-300">
                <div class="text-2xl font-bold text-green-600">{{ $statusBreakdown['paid'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">✅ Paid Installments</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center hover:shadow-xl transition-all duration-300">
                <div class="text-2xl font-bold text-yellow-600">{{ $statusBreakdown['partial'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">🔶 Partial Payments</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-4 text-center hover:shadow-xl transition-all duration-300">
                <div class="text-2xl font-bold text-red-600">{{ $statusBreakdown['pending'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">⏳ Pending</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 hover:shadow-2xl transition-all duration-300">
            <form method="GET" action="{{ route('admin.fee-reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Section</label>
                    <select name="class_section_id" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($classSections as $section)
                            <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <select name="student_id" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Students</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.fee-reports.index') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-all duration-300">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Class Wise Breakdown -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6 hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">🏫</span> Class Wise Breakdown
                </h2>
                <span class="text-sm text-gray-500">{{ count($classWiseData) }} classes</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Students</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Due</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paid</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Outstanding</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Collection %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($classWiseData as $data)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $data['section']->full_name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $data['student_count'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">₹{{ number_format($data['due'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-green-600">₹{{ number_format($data['paid'], 2) }}</td>
                            <td class="px-4 py-3 text-sm text-red-600">₹{{ number_format($data['outstanding'], 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 transition-all duration-1000" 
                                             style="width: {{ $data['collection_percentage'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $data['collection_percentage'] }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">No class data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Collection Trend -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6 hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📈</span> Monthly Collection Trend
                </h2>
            </div>
            <div class="p-6">
                @if($monthlyData->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($monthlyData as $month)
                            <div class="bg-gray-50 rounded-xl p-4 text-center hover:shadow-md transition-all duration-300">
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($month->month)->format('M Y') }}</div>
                                <div class="text-xl font-bold text-indigo-600">₹{{ number_format($month->collected, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-400 py-4">No monthly data available</div>
                @endif
            </div>
        </div>

        <!-- Student Wise Details -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">👤</span> Student Wise Details
                </h2>
                <span class="text-sm text-gray-500">{{ $studentWise->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fee Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paid</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Due Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($studentWise as $item)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $item->student->full_name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $item->student->admission_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $item->feeSubmissionType->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm font-semibold">₹{{ number_format($item->amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-green-600">₹{{ number_format($item->paid_amount, 2) }}</td>
                            <td class="px-4 py-3">
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
                            <td class="px-4 py-3 text-sm {{ $item->due_date && $item->due_date->isPast() && $item->status != 'paid' ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $item->due_date ? $item->due_date->format('d M Y') : 'N/A' }}
                                @if($item->due_date && $item->due_date->isPast() && $item->status != 'paid')
                                    <span class="text-xs text-red-500 ml-1">(Overdue)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.fee-reports.student', $item->student_id) }}" 
                                   class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition-all duration-200">
                                    👁️ View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">No records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $studentWise->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection