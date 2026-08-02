@extends('layouts.app')
@section('title', 'Exam Dashboard')

@section('content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .exam-card {
        transition: all 0.3s ease;
    }
    .exam-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="mb-8 animate-fade-up">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        📋 Exam Dashboard
                    </h1>
                    <p class="text-gray-500 mt-1">Manage exams, marks, and results</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.exams.create') }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Exam
                    </a>
                    <a href="{{ route('admin.exams.reports.index') }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Reports
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fade-up" style="animation-delay: 0.1s">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                    <select name="exam_type_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500">
                        <option value="">All Types</option>
                        @foreach($examTypes as $type)
                        <option value="{{ $type->id }}" {{ request('exam_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exam Group</label>
                    <select name="exam_group_id" class="w-full rounded-lg border-gray-300">
                        <option value="">All Groups</option>
                        @foreach($examGroups as $group)
                        <option value="{{ $group->id }}" {{ request('exam_group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Section</label>
                    <select name="class_section_id" class="w-full rounded-lg border-gray-300">
                        <option value="">All Sections</option>
                        @foreach($classSections as $section)
                        <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 w-full">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 w-full text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 animate-fade-up" style="animation-delay: 0.2s">
            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500 font-medium">Total Exams</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalExams }}</p>
                <p class="text-xs text-gray-400 mt-1">All time</p>
            </div>
            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500 font-medium">Published</p>
                <p class="text-3xl font-bold text-green-600">{{ $publishedExams }}</p>
                <p class="text-xs text-gray-400 mt-1">Available to students</p>
            </div>
            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500 font-medium">Upcoming</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $upcomingExams }}</p>
                <p class="text-xs text-gray-400 mt-1">Starting soon</p>
            </div>
            <div class="stat-card bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500 font-medium">Sections</p>
                <p class="text-3xl font-bold text-purple-600">{{ $classSections->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">Active classes</p>
            </div>
        </div>

        {{-- Exams List --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-fade-up" style="animation-delay: 0.3s">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">📋 Exams List</h2>
                <span class="text-sm text-gray-500">{{ $exams->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($exams as $exam)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $exam->name }}</div>
                                <div class="text-xs text-gray-400">Group: {{ $exam->examGroup->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $exam->examType->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $exam->classSection->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm">{{ $exam->start_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">to {{ $exam->end_date->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($exam->is_published)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.exams.show', $exam->id) }}" 
                                       class="text-blue-600 hover:text-blue-800" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.exams.marks-entry', $exam->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-800" title="Enter Marks">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    @if($exam->is_published)
                                        <a href="{{ route('admin.exams.unpublish', $exam->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-800" title="Unpublish"
                                           onclick="return confirm('Unpublish this exam?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.exams.publish', $exam->id) }}" 
                                           class="text-green-600 hover:text-green-800" title="Publish"
                                           onclick="return confirm('Publish this exam?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No exams found</h3>
                                <p class="mt-1 text-sm text-gray-500">Create your first exam to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                {{ $exams->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection