@extends('layouts.app')

@section('title', 'Select Class for Attendance')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('admin.studentattendance.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-indigo-600 mr-2"></i>Select Class for Attendance
            </h1>
            <p class="text-gray-500 mt-1">Choose a class section to mark attendance</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-school text-indigo-600 mr-2"></i>Available Classes
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stream</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($classSections as $section)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-medium">{{ $section->class->grade->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $section->section_name }}</td>
                            <td class="px-6 py-4">{{ $section->class->grade->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $section->class->stream->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $section->student_count ?? 0 }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.studentattendance.create', ['class_section_id' => $section->id]) }}" 
                                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                    <i class="fas fa-check mr-1"></i> Mark Attendance
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-8 text-gray-500">No class sections found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection