@extends('layouts.app')

@section('title', 'Exam Results Analysis')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">
    
    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 shadow-lg shadow-indigo-100">
        <div class="p-6 md:p-8 lg:p-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fas fa-chart-bar text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Exam Results Analysis</h1>
                        <p class="text-white/80 text-sm mt-1">Analyze results student-wise, subject-wise, and section-wise</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10">
                        <i class="fas fa-users"></i>
                        <span>{{ \App\Models\Student::count() }} Students</span>
                    </span>
                    <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10">
                        <i class="fas fa-book"></i>
                        <span>{{ \App\Models\Subject::count() }} Subjects</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Student Wise -->
        <a href="{{ route('admin.exam-results.student-wise') }}" class="group">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-4 mb-4">
                    <div class="bg-indigo-50 text-indigo-600 rounded-xl p-3.5 transition-colors group-hover:bg-indigo-100">
                        <i class="fas fa-user-graduate text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Student Wise</h3>
                </div>
                <p class="text-sm text-slate-500">View individual student performance across multiple tests and subjects</p>
                <div class="mt-4 flex items-center text-indigo-600 text-sm font-semibold group-hover:underline">
                    <span>View Analysis</span>
                    <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </div>
            </div>
        </a>

        <!-- Subject Wise -->
        <a href="{{ route('admin.exam-results.subject-wise') }}" class="group">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-4 mb-4">
                    <div class="bg-emerald-50 text-emerald-600 rounded-xl p-3.5 transition-colors group-hover:bg-emerald-100">
                        <i class="fas fa-book text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Subject Wise</h3>
                </div>
                <p class="text-sm text-slate-500">Analyze subject performance across sections and students</p>
                <div class="mt-4 flex items-center text-emerald-600 text-sm font-semibold group-hover:underline">
                    <span>View Analysis</span>
                    <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </div>
            </div>
        </a>

        <!-- Section Wise -->
        <a href="{{ route('admin.exam-results.section-wise') }}" class="group">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-4 mb-4">
                    <div class="bg-amber-50 text-amber-600 rounded-xl p-3.5 transition-colors group-hover:bg-amber-100">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Section Wise</h3>
                </div>
                <p class="text-sm text-slate-500">Compare section performance across subjects and tests</p>
                <div class="mt-4 flex items-center text-amber-600 text-sm font-semibold group-hover:underline">
                    <span>View Analysis</span>
                    <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Compare Tests -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="bg-purple-50 text-purple-600 rounded-xl p-3.5">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Compare Tests</h3>
        </div>
        <p class="text-sm text-slate-500 mb-4">Compare student performance across multiple tests</p>
        <form method="GET" action="{{ route('admin.exam-results.compare-tests') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Select Student</label>
                <select name="student_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                    <option value="">Search student...</option>
                    @foreach(\App\Models\Student::where('status', 'Active')->get() as $student)
                        <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-semibold text-slate-700 mb-1">Academic Session</label>
                <select name="session_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-purple-600 text-white text-sm font-semibold rounded-xl px-6 py-2.5 shadow-md shadow-purple-100 hover:bg-purple-700 transition-all">
                <i class="fas fa-chart-line"></i> Compare
            </button>
        </form>
    </div>
</div>
@endsection