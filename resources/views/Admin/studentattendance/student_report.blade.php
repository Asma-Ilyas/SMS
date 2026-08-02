@extends('layouts.app')

@section('title', 'Student Attendance Report')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center mb-6">
            <div>
                <a href="{{ route('admin.studentattendance.report') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block no-print">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Report
                </a>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-user-graduate text-indigo-600 mr-2"></i>Student Attendance Report
                </h1>
            </div>
            <div class="flex gap-2 mt-2 md:mt-0 no-print">
                <a href="{{ route('admin.studentattendance.export', ['student_id' => $student->id, 'month' => $month]) }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <i class="fas fa-file-excel mr-1"></i> Export CSV
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600 rounded-2xl shadow-lg p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="bg-white/20 rounded-full p-4">
                    <i class="fas fa-user-graduate text-5xl"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">{{ $student->first_name }} {{ $student->last_name }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3 text-indigo-100">
                        <div>
                            <span class="text-xs opacity-75">Student ID</span>
                            <p class="font-semibold text-white">{{ $student->id }}</p>
                        </div>
                        <div>
                            <span class="text-xs opacity-75">Class</span>
                            <p class="font-semibold text-white">{{ $student->classSection->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs opacity-75">Roll Number</span>
                            <p class="font-semibold text-white">{{ $student->roll_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs opacity-75">Admission No</span>
                            <p class="font-semibold text-white">{{ $student->admission_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month Filter -->
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-8 no-print">
            <form method="GET" action="{{ route('admin.studentattendance.student', $student->id) }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                    <select name="month" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        @foreach($availableMonths as $availableMonth)
                            <option value="{{ $availableMonth }}" {{ $month == $availableMonth ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($availableMonth)->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.studentattendance.student', $student->id) }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            </form>
        </div>

        <!-- ALL TIME STATISTICS -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>All Time Statistics
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">✅ Present</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">❌ Absent</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">⏰ Late</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">🌓 Half Day</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">📝 Leave</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">🎉 Holiday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">📋 On Duty</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Attendance %</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($monthlyStats as $stat)
                        @php
                            $total = $stat->total ?? 1;
                            $attended = ($stat->present ?? 0) + ($stat->late ?? 0) + ($stat->half_day ?? 0) + ($stat->on_duty ?? 0);
                            $percentage = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
                            $attendanceClass = $percentage >= 90 ? 'bg-green-100 text-green-800' : 
                                             ($percentage >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            $statusIcon = $percentage >= 90 ? '✅' : ($percentage >= 75 ? '⚠️' : '❌');
                            $statusText = $percentage >= 90 ? 'Excellent' : ($percentage >= 75 ? 'Good' : 'Needs Improvement');
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $month == $stat->month ? 'bg-indigo-50' : '' }}">
                            <td class="px-4 py-3 font-semibold text-gray-700">
                                {{ \Carbon\Carbon::parse($stat->month)->format('F Y') }}
                                @if($month == $stat->month)
                                    <span class="text-xs bg-indigo-200 text-indigo-800 px-2 py-0.5 rounded ml-1">Selected</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">{{ $stat->total }}</td>
                            <td class="px-4 py-3 text-center text-green-600">{{ $stat->present ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-red-600">{{ $stat->absent ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-yellow-600">{{ $stat->late ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-orange-600">{{ $stat->half_day ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-purple-600">{{ $stat->leave ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-blue-600">{{ $stat->holiday ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-teal-600">{{ $stat->on_duty ?? 0 }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $percentage }}%</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $attendanceClass }}">
                                    {{ $statusIcon }} {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center py-8 text-gray-500">No attendance records found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SELECTED MONTH STATISTICS -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>
                {{ \Carbon\Carbon::parse($month)->format('F Y') }} - Monthly Summary
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-200">
                    <div class="text-2xl font-bold text-gray-800">{{ $summary['total'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Total Days</div>
                </div>
                <div class="bg-green-50 rounded-xl p-3 text-center border border-green-200">
                    <div class="text-2xl font-bold text-green-600">{{ $summary['present'] ?? 0 }}</div>
                    <div class="text-xs text-green-600">✅ Present</div>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center border border-red-200">
                    <div class="text-2xl font-bold text-red-600">{{ $summary['absent'] ?? 0 }}</div>
                    <div class="text-xs text-red-600">❌ Absent</div>
                </div>
                <div class="bg-yellow-50 rounded-xl p-3 text-center border border-yellow-200">
                    <div class="text-2xl font-bold text-yellow-600">{{ $summary['late'] ?? 0 }}</div>
                    <div class="text-xs text-yellow-600">⏰ Late</div>
                </div>
                <div class="bg-orange-50 rounded-xl p-3 text-center border border-orange-200">
                    <div class="text-2xl font-bold text-orange-600">{{ $summary['half_day'] ?? 0 }}</div>
                    <div class="text-xs text-orange-600">🌓 Half Day</div>
                </div>
                <div class="bg-purple-50 rounded-xl p-3 text-center border border-purple-200">
                    <div class="text-2xl font-bold text-purple-600">{{ $summary['leave'] ?? 0 }}</div>
                    <div class="text-xs text-purple-600">📝 Leave</div>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 text-center border border-blue-200">
                    <div class="text-2xl font-bold text-blue-600">{{ $summary['holiday'] ?? 0 }}</div>
                    <div class="text-xs text-blue-600">🎉 Holiday</div>
                </div>
                <div class="bg-indigo-50 rounded-xl p-3 text-center border border-indigo-200">
                    @php
                        $total = $summary['total'] ?? 1;
                        $present = $summary['present'] ?? 0;
                        $attended = $present + ($summary['late'] ?? 0) + ($summary['half_day'] ?? 0) + ($summary['on_duty'] ?? 0);
                        $percentage = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
                    @endphp
                    <div class="text-2xl font-bold text-indigo-600">{{ $percentage }}%</div>
                    <div class="text-xs text-indigo-600">Attendance %</div>
                </div>
            </div>
        </div>

        <!-- REASONS BREAKDOWN -->
        @if(!empty($halfDayReasons) || !empty($lateReasons))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @if(!empty($halfDayReasons))
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h4 class="text-sm font-semibold text-orange-800 mb-3">
                    <i class="fas fa-clock text-orange-500 mr-2"></i> Half Day Reasons
                    <span class="text-xs text-gray-500 ml-2">({{ count($halfDayReasons) }})</span>
                </h4>
                <div class="max-h-48 overflow-y-auto">
                    @foreach($halfDayReasons as $date => $reason)
                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                        <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                        <span class="text-sm text-orange-600 font-medium">{{ $reason }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($lateReasons))
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h4 class="text-sm font-semibold text-yellow-800 mb-3">
                    <i class="fas fa-clock text-yellow-500 mr-2"></i> Late Arrival Reasons
                    <span class="text-xs text-gray-500 ml-2">({{ count($lateReasons) }})</span>
                </h4>
                <div class="max-h-48 overflow-y-auto">
                    @foreach($lateReasons as $date => $reason)
                    <div class="flex justify-between items-center border-b border-gray-100 py-2">
                        <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                        <span class="text-sm text-yellow-600 font-medium">{{ $reason }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- DETAILED ATTENDANCE HISTORY -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex flex-wrap items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-list-ul text-indigo-600 mr-2"></i>
                    Detailed Attendance History - {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                </h3>
                <span class="text-sm text-gray-500 mt-2 md:mt-0">
                    <i class="fas fa-user-graduate mr-1"></i>
                    {{ $attendances->total() }} records found
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Half Day Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendances as $att)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $att->date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $att->date->format('l') }}</td>
                            <td class="px-4 py-3">
                                @switch($att->status)
                                    @case('present') <span class="text-green-600 font-medium">✅ Present</span> @break
                                    @case('absent') <span class="text-red-600 font-medium">❌ Absent</span> @break
                                    @case('late') <span class="text-yellow-600 font-medium">⏰ Late</span> @break
                                    @case('half_day') <span class="text-orange-600 font-medium">🌓 Half Day</span> @break
                                    @case('leave') <span class="text-purple-600 font-medium">📝 Leave</span> @break
                                    @case('holiday') <span class="text-blue-600 font-medium">🎉 Holiday</span> @break
                                    @case('on_duty') <span class="text-teal-600 font-medium">📋 On Duty</span> @break
                                    @default <span class="text-gray-400">-</span>
                                @endswitch
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                @if($att->status == 'half_day')
                                    {{ $att->half_day_type ?? '-' }}
                                    @if($att->half_day_in_time && $att->half_day_out_time)
                                        <br><span class="text-xs text-gray-400">({{ $att->half_day_in_time }} - {{ $att->half_day_out_time }})</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 max-w-xs">
                                @if($att->status == 'half_day')
                                    {{ $att->half_day_reason ?? $att->remarks ?? '-' }}
                                @elseif($att->status == 'late')
                                    {{ $att->late_reason ?? $att->remarks ?? '-' }}
                                @elseif($att->status == 'leave')
                                    {{ $att->leave_reason ?? $att->remarks ?? '-' }}
                                @else
                                    {{ $att->remarks ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $att->check_in ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $att->check_out ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($att->is_approved)
                                    <span class="text-green-600">✅ Yes</span>
                                @else
                                    <span class="text-red-600">❌ No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $att->teacher->full_name ?? 'Admin' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-12 text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                                No attendance records found for this month
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .shadow-lg { box-shadow: none !important; }
        .rounded-2xl { border-radius: 0 !important; }
        .border { border-color: #e5e7eb !important; }
        .bg-gradient-to-r { background: #4f46e5 !important; }
        .bg-gray-50 { background: #f9fafb !important; }
        .bg-white { background: white !important; }
    }
    
    .max-h-48 {
        max-height: 12rem;
    }
    
    .overflow-y-auto {
        overflow-y: auto;
    }
    
    .max-w-xs {
        max-width: 12rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit form when month changes
    document.querySelector('select[name="month"]')?.addEventListener('change', function() {
        this.closest('form').submit();
    });
</script>
@endpush
@endsection