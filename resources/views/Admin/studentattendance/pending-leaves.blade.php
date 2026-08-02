{{-- resources/views/admin/studentattendance/pending-leaves.blade.php --}}

@extends('layouts.app')

@section('title', 'Pending Leave Requests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📋 Pending Requests</h1>
                    <p class="text-gray-500 mt-1">Leave and half-day requests awaiting approval</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.studentattendance.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back to Dashboard
                    </a>
                    <a href="{{ route('admin.studentattendance.report') }}" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition">
                        📊 Reports
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if($pendingLeaves->count() > 0)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white">
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span>⏳</span> Pending Requests
                        <span class="ml-2 bg-white text-yellow-600 px-3 py-1 rounded-full text-sm">
                            {{ $pendingLeaves->count() }}
                        </span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Class</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingLeaves as $attendance)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $attendance->student->first_name ?? 'N/A' }} {{ $attendance->student->last_name ?? '' }}</div>
                                    <div class="text-xs text-gray-400">Roll: {{ $attendance->student->roll_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $attendance->classSection->full_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($attendance->status == 'half_day')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">🌓 Half Day</span>
                                        <div class="text-xs text-gray-500 mt-1">{{ $attendance->half_day_type_label }}</div>
                                        @if($attendance->half_day_in_time && $attendance->half_day_out_time)
                                            <div class="text-xs text-gray-400">{{ $attendance->half_day_in_time }} - {{ $attendance->half_day_out_time }}</div>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">📝 Leave</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $attendance->date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($attendance->status == 'half_day')
                                        {{ $attendance->half_day_reason ?? 'N/A' }}
                                    @else
                                        {{ $attendance->leave_reason ?? 'N/A' }}
                                    @endif
                                    @if($attendance->remarks)
                                        <div class="text-xs text-gray-400 mt-1">{{ $attendance->remarks }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 flex-wrap">
                                        <form action="{{ route('admin.studentattendance.approve-leave', $attendance->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm rounded transition">
                                                ✅ Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.studentattendance.reject-leave', $attendance->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded transition" onclick="return confirm('Reject this request?')">
                                                ❌ Reject
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.studentattendance.show', $attendance->id) }}" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded transition">
                                            👁️ View
                                        </a>
                                        <a href="{{ route('admin.studentattendance.edit', $attendance->id) }}" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded transition">
                                            ✏️ Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-xl font-medium text-gray-900">No Pending Requests</h3>
                <p class="mt-2 text-gray-500">All leave and half-day requests have been reviewed.</p>
                <a href="{{ route('admin.studentattendance.index') }}" class="mt-4 inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    ← Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection