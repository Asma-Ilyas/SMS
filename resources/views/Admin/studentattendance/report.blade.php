{{-- resources/views/admin/studentattendance/report.blade.php --}}

@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        📊 Attendance Reports
                    </h1>
                    <p class="text-gray-500 mt-1">View and analyze attendance records</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.studentattendance.section-wise-report') }}" 
                       class="px-5 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        📈 Section Wise
                    </a>
                    <a href="{{ route('admin.studentattendance.index') }}" 
                       class="px-5 py-2.5 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300">
                        ← Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6 hover:shadow-2xl transition-all duration-300">
            <form method="GET" action="{{ route('admin.studentattendance.report') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <select name="class_section_id" class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
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
                    <select name="student_id" class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" 
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" 
                           class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approval</label>
                    <select name="is_approved" class="w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">All</option>
                        <option value="1" {{ request('is_approved') == '1' ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ request('is_approved') == '0' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.studentattendance.report') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-xl transition-all duration-300">
                        Reset
                    </a>
                    <a href="{{ route('admin.studentattendance.export', request()->all()) }}" 
                       class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
                        📥 Export
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        @if($summary)
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <div class="text-xl font-bold text-gray-800">{{ count($summary) }}</div>
                <div class="text-xs text-gray-500">Students</div>
            </div>
            <div class="bg-green-50 rounded-xl shadow p-4 text-center border border-green-200">
                <div class="text-xl font-bold text-green-600">
                    {{ collect($summary)->sum('present') }}
                </div>
                <div class="text-xs text-green-600">✅ Present</div>
            </div>
            <div class="bg-red-50 rounded-xl shadow p-4 text-center border border-red-200">
                <div class="text-xl font-bold text-red-600">
                    {{ collect($summary)->sum('absent') }}
                </div>
                <div class="text-xs text-red-600">❌ Absent</div>
            </div>
            <div class="bg-yellow-50 rounded-xl shadow p-4 text-center border border-yellow-200">
                <div class="text-xl font-bold text-yellow-600">
                    {{ collect($summary)->sum('late') }}
                </div>
                <div class="text-xs text-yellow-600">⏰ Late</div>
            </div>
            <div class="bg-blue-50 rounded-xl shadow p-4 text-center border border-blue-200">
                <div class="text-xl font-bold text-blue-600">
                    {{ collect($summary)->sum('half_day') }}
                </div>
                <div class="text-xs text-blue-600">🌓 Half Day</div>
            </div>
            <div class="bg-purple-50 rounded-xl shadow p-4 text-center border border-purple-200">
                <div class="text-xl font-bold text-purple-600">
                    {{ collect($summary)->sum('leave') }}
                </div>
                <div class="text-xs text-purple-600">📝 Leave</div>
            </div>
        </div>
        @endif

        <!-- Results Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="text-2xl">📋</span> Attendance Records
                    <span class="text-sm font-normal text-gray-500 ml-2">({{ $attendances->total() }} records)</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Details</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attendances as $attendance)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $attendance->student->first_name ?? 'N/A' }} {{ $attendance->student->last_name ?? '' }}</div>
                                    <div class="text-xs text-gray-400">Roll: {{ $attendance->student->roll_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $attendance->classSection->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{!! $attendance->status_label !!}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($attendance->status == 'half_day')
                                        <div class="text-xs">
                                            <span class="font-medium">Type:</span> {{ $attendance->half_day_type_label }}
                                            <br>
                                            <span class="font-medium">Reason:</span> {{ $attendance->half_day_reason ?? 'N/A' }}
                                        </div>
                                    @elseif($attendance->status == 'late')
                                        <div class="text-xs">
                                            <span class="font-medium">Arrival:</span> {{ $attendance->late_arrival_time ?? 'N/A' }}
                                            <br>
                                            <span class="font-medium">Min:</span> {{ $attendance->late_minutes ?? 0 }} min
                                            <br>
                                            <span class="font-medium">Reason:</span> {{ $attendance->late_reason ?? 'N/A' }}
                                        </div>
                                    @elseif($attendance->status == 'leave')
                                        <div class="text-xs">
                                            <span class="font-medium">Reason:</span> {{ $attendance->leave_reason ?? 'N/A' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.studentattendance.show', $attendance->id) }}" 
                                           class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg transition-all duration-200">
                                            👁️ View
                                        </a>
                                        <a href="{{ route('admin.studentattendance.edit', $attendance->id) }}" 
                                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-lg transition-all duration-200">
                                            ✏️ Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2">No attendance records found</p>
                                    <p class="text-sm">Try adjusting your filters</p>
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
    </div>
</div>
@endsection