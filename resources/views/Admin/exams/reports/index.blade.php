@extends('layouts.app')
@section('title', 'Exam Reports')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        📊 Exam Reports
                    </h1>
                    <p class="text-gray-500 mt-1">Comprehensive exam analysis and reporting</p>
                </div>
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    ← Back to Exams
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Total Exams</p>
                <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Exam::count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                <p class="text-sm text-gray-500">Published</p>
                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Exam::where('is_published', true)->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500">Sections</p>
                <p class="text-2xl font-bold text-purple-600">{{ \App\Models\ClassSection::count() }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Students</p>
                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Student::count() }}</p>
            </div>
        </div>

        {{-- Report Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('admin.exams.reports.class-wise') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="text-4xl mb-3">🏫</div>
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600">Class-wise Report</h3>
                <p class="text-sm text-gray-500 mt-1">View results by class section</p>
                <div class="mt-4 flex items-center text-indigo-600 group-hover:translate-x-2 transition">
                    View Report →
                </div>
            </a>

            <a href="{{ route('admin.exams.reports.student-wise') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="text-4xl mb-3">👨‍🎓</div>
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600">Student-wise Report</h3>
                <p class="text-sm text-gray-500 mt-1">View results by student</p>
                <div class="mt-4 flex items-center text-indigo-600 group-hover:translate-x-2 transition">
                    View Report →
                </div>
            </a>

            <a href="{{ route('admin.exams.reports.subject-wise') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="text-4xl mb-3">📖</div>
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600">Subject-wise Report</h3>
                <p class="text-sm text-gray-500 mt-1">View results by subject</p>
                <div class="mt-4 flex items-center text-indigo-600 group-hover:translate-x-2 transition">
                    View Report →
                </div>
            </a>

            <a href="{{ route('admin.exams.reports.performance-analysis') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="text-4xl mb-3">📈</div>
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600">Performance Analysis</h3>
                <p class="text-sm text-gray-500 mt-1">Analyze student performance trends</p>
                <div class="mt-4 flex items-center text-indigo-600 group-hover:translate-x-2 transition">
                    View Report →
                </div>
            </a>

            <a href="{{ route('admin.exams.reports.grade-distribution') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="text-4xl mb-3">📊</div>
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600">Grade Distribution</h3>
                <p class="text-sm text-gray-500 mt-1">View grade distribution across exams</p>
                <div class="mt-4 flex items-center text-indigo-600 group-hover:translate-x-2 transition">
                    View Report →
                </div>
            </a>

            <a href="{{ route('admin.exams.reports.class-wise') }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition group">
                <div class="text-4xl mb-3">📑</div>
                <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600">Export Reports</h3>
                <p class="text-sm text-gray-500 mt-1">Export reports in CSV/Excel format</p>
                <div class="mt-4 flex items-center text-indigo-600 group-hover:translate-x-2 transition">
                    Export →
                </div>
            </a>
        </div>

        {{-- Recent Results --}}
        <div class="mt-8 bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 Recent Exam Results</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Avg %</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $recentExams = \App\Models\Exam::with(['classSection', 'examType'])->where('is_published', true)->latest()->take(5)->get(); @endphp
                        @forelse($recentExams as $exam)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $exam->name }}</td>
                            <td class="px-6 py-4">{{ $exam->classSection->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $exam->start_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">{{ $exam->total_students }}</td>
                            <td class="px-6 py-4 text-center">{{ number_format($exam->results->avg('percentage') ?? 0, 1) }}%</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✅ Published</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.exams.reports.class-wise', ['exam_id' => $exam->id]) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">View Report</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-8 text-gray-500">No published exams found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection