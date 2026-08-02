@extends('layouts.app')
@section('title', 'Performance Analysis')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('admin.exams.reports.index') }}" class="hover:text-indigo-600 transition">← Back</a>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700 font-medium">Performance Analysis</span>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                <h1 class="text-3xl font-bold">📈 Performance Analysis</h1>
                <p class="text-indigo-100">Analyze student performance trends</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                    <select name="exam_type_id" class="w-full rounded-lg border-gray-300">
                        <option value="">All Types</option>
                        @foreach($examTypes as $type)
                        <option value="{{ $type->id }}" {{ request('exam_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date', $fromDate) }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date', $toDate) }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">🔍 Analyze</button>
                    <a href="{{ route('admin.exams.reports.performance-analysis') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Reset</a>
                </div>
            </form>
        </div>

        @if(!empty($stats))
        {{-- Stats Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Students</p>
                <p class="text-2xl font-bold text-gray-800">{{ count($stats) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Avg Performance</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format(collect($stats)->avg('average_percentage'), 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Pass Rate</p>
                <p class="text-2xl font-bold text-yellow-600">
                    {{ number_format(collect($stats)->avg('pass_count') / collect($stats)->avg('total_exams') * 100, 1) }}%
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500">Top Performer</p>
                <p class="text-lg font-bold text-purple-600 truncate">{{ $stats[0]['student']->first_name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Performance Table --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold text-gray-800">📊 Student Performance</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Exams</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Avg %</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Best</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Worst</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pass/Fail</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($stats as $index => $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.students.show', $stat['student']->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $stat['student']->first_name }} {{ $stat['student']->last_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $stat['total_exams'] }}</td>
                            <td class="px-6 py-4 text-center font-bold {{ $stat['average_percentage'] >= 70 ? 'text-green-600' : ($stat['average_percentage'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($stat['average_percentage'], 1) }}%
                            </td>
                            <td class="px-6 py-4 text-center text-green-600">{{ number_format($stat['best_percentage'], 1) }}%</td>
                            <td class="px-6 py-4 text-center text-red-600">{{ number_format($stat['worst_percentage'], 1) }}%</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-green-600">{{ $stat['pass_count'] }}</span>
                                <span class="text-gray-400">/</span>
                                <span class="text-red-600">{{ $stat['fail_count'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $avg = $stat['average_percentage'];
                                    if ($avg >= 80) { $badge = 'bg-green-100 text-green-800'; $label = '🌟 Excellent'; }
                                    elseif ($avg >= 70) { $badge = 'bg-blue-100 text-blue-800'; $label = '✅ Good'; }
                                    elseif ($avg >= 60) { $badge = 'bg-yellow-100 text-yellow-800'; $label = '📊 Average'; }
                                    elseif ($avg >= 50) { $badge = 'bg-orange-100 text-orange-800'; $label = '⚠️ Below Avg'; }
                                    else { $badge = 'bg-red-100 text-red-800'; $label = '❌ Poor'; }
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $badge }}">{{ $label }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif(request('class_section_id'))
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Data Found</h3>
            <p class="mt-2 text-sm text-gray-500">No performance data available for the selected filters.</p>
        </div>
        @endif
    </div>
</div>
@endsection