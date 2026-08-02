{{-- resources/views/admin/studentattendance/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-user-check text-indigo-600 mr-2"></i> Attendance Details
                    </h1>
                    <p class="text-gray-500 mt-1">
                        {{ $attendance->student->first_name ?? 'N/A' }} {{ $attendance->student->last_name ?? '' }} 
                        - {{ $attendance->date ? $attendance->date->format('l, F d, Y') : 'N/A' }}
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap no-print">
                    <!-- NEW: View Full Report Button -->
                    <a href="{{ route('admin.studentattendance.student', $attendance->student_id) }}" 
                       class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <i class="fas fa-chart-bar mr-1"></i> View Full Report
                    </a>
                    <a href="{{ route('admin.studentattendance.report') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <a href="{{ route('admin.studentattendance.edit', $attendance->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    @if(!$attendance->is_approved && in_array($attendance->status, ['leave', 'half_day']))
                    <form action="{{ route('admin.studentattendance.approve-leave', $attendance->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.studentattendance.reject-leave', $attendance->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition" onclick="return confirm('Reject this request?')">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.studentattendance.destroy', $attendance->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Are you sure you want to delete this attendance record?')">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600 rounded-2xl shadow-lg p-6 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="bg-white/20 rounded-full p-4">
                    <i class="fas fa-user-graduate text-5xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold">{{ $attendance->student->first_name ?? 'N/A' }} {{ $attendance->student->last_name ?? '' }}</h2>
                        <a href="{{ route('admin.studentattendance.student', $attendance->student_id) }}" 
                           class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full transition">
                            <i class="fas fa-external-link-alt mr-1"></i> View Full History
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3 text-indigo-100">
                        <div>
                            <span class="text-xs opacity-75">Student ID</span>
                            <p class="font-semibold text-white">{{ $attendance->student->id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs opacity-75">Class</span>
                            <p class="font-semibold text-white">{{ $attendance->classSection->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs opacity-75">Roll Number</span>
                            <p class="font-semibold text-white">{{ $attendance->student->roll_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs opacity-75">Date</span>
                            <p class="font-semibold text-white">{{ $attendance->date ? $attendance->date->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats - New Section -->
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-8">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">
                <i class="fas fa-chart-simple text-indigo-500 mr-2"></i> Quick Stats - {{ $attendance->student->first_name ?? '' }}
            </h4>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-800">{{ $attendance->student->attendances->count() ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Total Records</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-green-600">{{ $attendance->student->attendances->where('status', 'present')->count() ?? 0 }}</div>
                    <div class="text-xs text-gray-500">✅ Present</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-red-600">{{ $attendance->student->attendances->where('status', 'absent')->count() ?? 0 }}</div>
                    <div class="text-xs text-gray-500">❌ Absent</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-yellow-600">{{ $attendance->student->attendances->where('status', 'late')->count() ?? 0 }}</div>
                    <div class="text-xs text-gray-500">⏰ Late</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-orange-600">{{ $attendance->student->attendances->where('status', 'half_day')->count() ?? 0 }}</div>
                    <div class="text-xs text-gray-500">🌓 Half Day</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-purple-600">{{ $attendance->student->attendances->where('status', 'leave')->count() ?? 0 }}</div>
                    <div class="text-xs text-gray-500">📝 Leave</div>
                </div>
            </div>
        </div>

        <!-- Rest of the show page content... -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>Attendance Details
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-xs text-gray-400 font-medium uppercase tracking-wider">Status</label>
                        <p class="text-lg font-bold mt-1">
                            @switch($attendance->status)
                                @case('present') <span class="text-green-600">✅ Present</span> @break
                                @case('absent') <span class="text-red-600">❌ Absent</span> @break
                                @case('late') <span class="text-yellow-600">⏰ Late</span> @break
                                @case('half_day') <span class="text-orange-600">🌓 Half Day</span> @break
                                @case('leave') <span class="text-purple-600">📝 Leave</span> @break
                                @case('holiday') <span class="text-blue-600">🎉 Holiday</span> @break
                                @case('on_duty') <span class="text-teal-600">📋 On Duty</span> @break
                                @default <span class="text-gray-400">{{ $attendance->status ?? 'N/A' }}</span>
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 font-medium uppercase tracking-wider">Subject</label>
                        <p class="text-gray-800 font-medium">{{ $attendance->subject->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 font-medium uppercase tracking-wider">Approved</label>
                        <p class="text-lg font-bold mt-1">
                            @if($attendance->is_approved)
                                <span class="text-green-600">✅ Yes</span>
                            @else
                                <span class="text-yellow-600">⏳ Pending</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Half Day Details -->
                @if($attendance->status == 'half_day')
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-clock mr-1"></i> Half Day Details
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="text-xs text-gray-400">Type</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->half_day_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Reason</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->half_day_reason ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">In Time</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->half_day_in_time ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Out Time</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->half_day_out_time ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Late Details -->
                @if($attendance->status == 'late')
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h4 class="font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-clock mr-1"></i> Late Details
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="text-xs text-gray-400">Arrival Time</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->late_arrival_time ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400">Minutes Late</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->late_minutes ?? 0 }} min</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-400">Reason</label>
                            <p class="text-gray-800 font-medium">{{ $attendance->late_reason ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Leave Details -->
                @if($attendance->status == 'leave')
                <div class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <h4 class="font-semibold text-purple-800 mb-2">
                        <i class="fas fa-file-alt mr-1"></i> Leave Details
                    </h4>
                    <div>
                        <label class="text-xs text-gray-400">Reason</label>
                        <p class="text-gray-800 font-medium">{{ $attendance->leave_reason ?? $attendance->remarks ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif

                <!-- Check In/Out -->
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-gray-400 font-medium uppercase tracking-wider">Check In</label>
                        <p class="text-gray-800 font-medium">{{ $attendance->check_in ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 font-medium uppercase tracking-wider">Check Out</label>
                        <p class="text-gray-800 font-medium">{{ $attendance->check_out ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mt-4">
                    <label class="text-xs text-gray-400 font-medium uppercase tracking-wider">Remarks</label>
                    <p class="text-gray-800 mt-1 p-3 bg-gray-50 rounded-lg">{{ $attendance->remarks ?? 'No remarks' }}</p>
                </div>

                <!-- Approval Info -->
                @if($attendance->is_approved && $attendance->approved_by)
                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200 text-sm">
                    <span class="text-green-700">
                        <i class="fas fa-check-circle mr-1"></i> 
                        Approved by: {{ $attendance->approvedBy->first_name ?? 'N/A' }} {{ $attendance->approvedBy->last_name ?? '' }}
                    </span>
                    <span class="text-gray-400 ml-2">
                        at {{ $attendance->approved_at ? $attendance->approved_at->format('d M Y H:i') : 'N/A' }}
                    </span>
                </div>
                @endif

                <!-- Marked By -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                    <span class="text-gray-500">
                        <i class="fas fa-user mr-1"></i>
                        Marked by: {{ $attendance->markedBy->first_name ?? 'N/A' }} {{ $attendance->markedBy->last_name ?? '' }}
                    </span>
                    <span class="text-gray-400 ml-2">
                        at {{ $attendance->created_at ? $attendance->created_at->format('d M Y H:i') : 'N/A' }}
                    </span>
                </div>

                <!-- Teacher -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                    <span class="text-gray-500">
                        <i class="fas fa-chalkboard-teacher mr-1"></i>
                        Teacher: {{ $attendance->teacher->first_name ?? 'N/A' }} {{ $attendance->teacher->last_name ?? '' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Recent Attendance Preview -->
        @if($attendance->student->attendances->count() > 1)
        <div class="mt-8 bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h4 class="text-sm font-semibold text-gray-700">
                    <i class="fas fa-history text-indigo-500 mr-2"></i> Recent Attendance Records
                </h4>
                <a href="{{ route('admin.studentattendance.student', $attendance->student_id) }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                    View All →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($attendance->student->attendances->sortByDesc('date')->take(5) as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm">{{ $record->date->format('d M Y') }}</td>
                            <td class="px-4 py-2 text-sm">
                                @switch($record->status)
                                    @case('present') <span class="text-green-600">✅ Present</span> @break
                                    @case('absent') <span class="text-red-600">❌ Absent</span> @break
                                    @case('late') <span class="text-yellow-600">⏰ Late</span> @break
                                    @case('half_day') <span class="text-orange-600">🌓 Half Day</span> @break
                                    @case('leave') <span class="text-purple-600">📝 Leave</span> @break
                                    @default <span class="text-gray-400">{{ $record->status }}</span>
                                @endswitch
                            </td>
                            <td class="px-4 py-2 text-sm">{{ $record->subject->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('admin.studentattendance.show', $record->id) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
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
    }
</style>
@endpush
@endsection