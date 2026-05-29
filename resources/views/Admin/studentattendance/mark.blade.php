@extends('layouts.app')
@section('title', 'Mark Student Attendance')
@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">✏️ Mark Attendance</h1>
            <p class="text-gray-600 mt-1">Record student attendance for the selected class</p>
        </div>
        <a href="{{ route('admin.studentattendance.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Classes
        </a>
    </div>

    {{-- Info Card --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm p-6 mb-8 border border-blue-100">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-sm text-gray-500">📚 Class</span>
                <p class="font-semibold text-gray-800 text-lg">{{ $classNameDisplay }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">👥 Section</span>
                <p class="font-semibold text-gray-800 text-lg">{{ $section }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">📖 Subject (First Period)</span>
                <p class="font-semibold text-gray-800 text-lg">{{ $subject->name ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">📅 Date</span>
                <p class="font-semibold text-gray-800 text-lg">{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">👨‍🏫 Marked by</span>
                <p class="font-semibold text-gray-800 text-lg">{{ $staffName }}</p>
            </div>
        </div>
    </div>

    {{-- Attendance Form --}}
    <form method="POST" action="{{ route('admin.studentattendance.store') }}" class="bg-white rounded-xl shadow-md overflow-hidden">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">{{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $student->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select name="attendance[{{ $student->id }}][status]" 
                                    required
                                    class="block w-32 px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="present" {{ ($attendances[$student->id]->status ?? '') == 'present' ? 'selected' : '' }}>✅ Present</option>
                                <option value="absent" {{ ($attendances[$student->id]->status ?? '') == 'absent' ? 'selected' : '' }}>❌ Absent</option>
                                <option value="late" {{ ($attendances[$student->id]->status ?? '') == 'late' ? 'selected' : '' }}>⏰ Late</option>
                                <option value="half_day" {{ ($attendances[$student->id]->status ?? '') == 'half_day' ? 'selected' : '' }}>🌓 Half Day</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" 
                                   name="attendance[{{ $student->id }}][remarks]" 
                                   value="{{ $attendances[$student->id]->remarks ?? '' }}"
                                   placeholder="Optional remark"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Form Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('admin.studentattendance.index') }}" 
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm">
                💾 Save Attendance
            </button>
        </div>
    </form>
</div>
@endsection