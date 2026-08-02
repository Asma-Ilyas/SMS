{{-- resources/views/admin/students/attendance.blade.php --}}

@extends('layouts.app')

@section('title', 'Attendance History - ' . ($student->first_name ?? 'N/A') . ' ' . ($student->last_name ?? ''))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📋 Attendance History</h1>
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

        <!-- Attendance Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center hover:shadow-lg transition">
                <div class="text-2xl font-bold text-gray-800">{{ $summary['total'] ?? 0 }}</div>
                <div class="text-xs text-gray-500">Total Days</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border border-green-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-green-600">{{ $summary['present'] ?? 0 }}</div>
                <div class="text-xs text-green-600">✅ Present</div>
            </div>
            <div class="bg-red-50 rounded-xl shadow p-4 text-center border border-red-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-red-600">{{ $summary['absent'] ?? 0 }}</div>
                <div class="text-xs text-red-600">❌ Absent</div>
            </div>
            <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border border-yellow-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-yellow-600">{{ $summary['late'] ?? 0 }}</div>
                <div class="text-xs text-yellow-600">⏰ Late</div>
            </div>
            <div class="bg-blue-50 rounded-xl shadow p-4 text-center border border-blue-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-blue-600">{{ $summary['half_day'] ?? 0 }}</div>
                <div class="text-xs text-blue-600">🌓 Half Day</div>
            </div>
            <div class="bg-purple-50 rounded-xl shadow p-4 text-center border border-purple-200 hover:shadow-lg transition">
                <div class="text-2xl font-bold text-purple-600">{{ $summary['percentage'] ?? 0 }}%</div>
                <div class="text-xs text-purple-600">Attendance %</div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📋</span> Attendance Records
                </h2>
                <span class="text-sm text-gray-500">{{ $attendances->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Day</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Check In</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Check Out</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Half Day Details</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Late Details</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attendances as $attendance)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                {{ $attendance->date ? $attendance->date->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $attendance->date ? $attendance->date->format('l') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($attendance->status == 'present')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Present</span>
                                @elseif($attendance->status == 'absent')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">❌ Absent</span>
                                @elseif($attendance->status == 'late')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⏰ Late</span>
                                @elseif($attendance->status == 'half_day')
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">🌓 Half Day</span>
                                @elseif($attendance->status == 'leave')
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">📝 Leave</span>
                                @elseif($attendance->status == 'holiday')
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">🎉 Holiday</span>
                                @elseif($attendance->status == 'on_duty')
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">💼 On Duty</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ ucfirst($attendance->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->check_in ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->check_out ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($attendance->status == 'half_day')
                                    <div class="text-xs">
                                        <span class="font-medium">Type:</span> {{ $attendance->half_day_type_label ?? 'N/A' }}
                                        <br>
                                        <span class="font-medium">Reason:</span> {{ $attendance->half_day_reason ?? 'N/A' }}
                                        @if($attendance->half_day_in_time && $attendance->half_day_out_time)
                                            <br>
                                            <span class="font-medium">Time:</span> {{ $attendance->half_day_in_time }} - {{ $attendance->half_day_out_time }}
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($attendance->status == 'late')
                                    <div class="text-xs">
                                        <span class="font-medium">Arrival:</span> {{ $attendance->late_arrival_time ?? 'N/A' }}
                                        <br>
                                        <span class="font-medium">Minutes:</span> {{ $attendance->late_minutes ?? 0 }} min
                                        <br>
                                        <span class="font-medium">Reason:</span> {{ $attendance->late_reason ?? 'N/A' }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->remarks ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-2">No attendance records found for this student</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $attendances->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Monthly Summary -->
        @if($attendances->count() > 0)
        <div class="mt-6 bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📊</span> Monthly Attendance Summary
                </h2>
            </div>
            <div class="p-6">
                @php
                    $monthlyData = $attendances->groupBy(function($item) {
                        return $item->date->format('F Y');
                    });
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($monthlyData as $month => $records)
                        @php
                            $total = $records->count();
                            $present = $records->where('status', 'present')->count();
                            $absent = $records->where('status', 'absent')->count();
                            $late = $records->where('status', 'late')->count();
                            $halfDay = $records->where('status', 'half_day')->count();
                            $leave = $records->where('status', 'leave')->count();
                            $attended = $present + $late + $halfDay;
                            $percentage = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
                        @endphp
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-lg transition">
                            <h4 class="font-bold text-gray-800 mb-2">{{ $month }}</h4>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="bg-green-100 rounded-lg p-2">
                                    <div class="text-lg font-bold text-green-600">{{ $present }}</div>
                                    <div class="text-xs text-green-700">✅ Present</div>
                                </div>
                                <div class="bg-red-100 rounded-lg p-2">
                                    <div class="text-lg font-bold text-red-600">{{ $absent }}</div>
                                    <div class="text-xs text-red-700">❌ Absent</div>
                                </div>
                                <div class="bg-yellow-100 rounded-lg p-2">
                                    <div class="text-lg font-bold text-yellow-600">{{ $late }}</div>
                                    <div class="text-xs text-yellow-700">⏰ Late</div>
                                </div>
                                <div class="bg-blue-100 rounded-lg p-2">
                                    <div class="text-lg font-bold text-blue-600">{{ $halfDay }}</div>
                                    <div class="text-xs text-blue-700">🌓 Half Day</div>
                                </div>
                                <div class="bg-purple-100 rounded-lg p-2">
                                    <div class="text-lg font-bold text-purple-600">{{ $leave }}</div>
                                    <div class="text-xs text-purple-700">📝 Leave</div>
                                </div>
                                <div class="bg-indigo-100 rounded-lg p-2">
                                    <div class="text-lg font-bold text-indigo-600">{{ $percentage }}%</div>
                                    <div class="text-xs text-indigo-700">Attendance</div>
                                </div>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $percentage >= 75 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

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
}
</style>
@endpush
@endsection