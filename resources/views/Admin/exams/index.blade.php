@extends('layouts.app')
@section('title', 'Manage Exams')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Exams</h1>
        <div class="space-x-3">
            <a href="{{ route('admin.exam-results.multi-group-report.form') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Multi-Group Report</a>
            <a href="{{ route('admin.exams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New Exam</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Center</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Table</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Results</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->class->full_name ?? $exam->class->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->exam_center ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->start_date->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->end_date->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($exam->time_table)
                                <a href="{{ Storage::url($exam->time_table) }}" target="_blank" class="text-blue-600 underline">Download</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <!-- UPDATED Exam Results Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.exams.marks-entry', $exam) }}" class="text-indigo-600 underline mr-2">Enter Marks</a>
                            <a href="{{ route('admin.exam-results.bulk-print-form', $exam) }}" class="text-green-600 underline mr-2">Bulk Print</a>
<a href="{{ url('/admin/exam-results/exams/' . $exam->id . '/class-section-marksheet') }}" class="text-blue-600 underline">Class/Section PDF</a>                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $exam->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Delete this exam?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                            @if(!$exam->is_published)
                                <a href="{{ route('admin.exams.publish', $exam) }}" class="text-yellow-600 hover:text-yellow-900">Publish</a>
                            @else
                                <a href="{{ route('admin.exams.unpublish', $exam) }}" class="text-gray-600 hover:text-gray-900">Unpublish</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">No exams found. <a href="{{ route('admin.exams.create') }}" class="text-blue-600">Create your first exam</a>.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $exams->links() }}
        </div>
    </div>
</div>
@endsection