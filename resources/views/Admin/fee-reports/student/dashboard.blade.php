@extends('layouts.app')

@section('title', 'Student Reports Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30 p-4 md:p-6">

    <!-- ===== HERO SECTION ===== -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-6 md:p-8 lg:p-10 mb-6 shadow-2xl shadow-indigo-500/20">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 animate-pulse">
                    <svg class="w-7 h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white tracking-tight">Student Reports</h1>
                    <p class="text-indigo-100 text-sm md:text-base mt-1">Generate comprehensive student reports with ease</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10">
                    <span>👨‍🎓</span> {{ \App\Models\Student::count() }} Students
                </span>
                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10">
                    <span>📚</span> {{ \App\Models\Subject::count() }} Subjects
                </span>
                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10">
                    <span>📝</span> {{ \App\Models\Exam::where('is_published', true)->count() }} Exams
                </span>
            </div>
        </div>
    </div>

    <!-- ===== STATS CARDS ===== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Total Students</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-1">{{ \App\Models\Student::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                    👨‍🎓
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                    <span class="text-emerald-400">●</span> Active
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Active Students</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-emerald-600 mt-1">{{ \App\Models\Student::where('status', 'Active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                    ✅
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                    <span class="text-emerald-400">●</span> Enrolled
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Published Exams</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-purple-600 mt-1">{{ \App\Models\Exam::where('is_published', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                    📝
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">
                    <span class="text-purple-400">●</span> Active
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Fee Collected</p>
                    <p class="text-2xl md:text-3xl font-extrabold text-amber-600 mt-1">${{ number_format(\App\Models\StudentFeeInstallment::where('status', 'paid')->sum('paid_amount'), 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                    💰
                </div>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">
                    <span class="text-amber-400">●</span> Total
                </span>
            </div>
        </div>
    </div>

    <!-- ===== MAIN REPORT GENERATOR ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Report -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-2xl">📋</div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Complete Student Report</h3>
                    <p class="text-sm text-slate-500">All-in-one profile, attendance, academics & fees</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.reports.student.generate') }}" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Select Student <span class="text-red-500">*</span></label>
                    <select name="student_id" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700 text-sm transition-all duration-200" required>
                        <option value="">🔍 Search for a student...</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->admission_number }}) - {{ $student->current_class }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Academic Session</label>
                        <select name="session_id" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700 text-sm transition-all duration-200">
                            <option value="">All Sessions</option>
                            @foreach($academicSessions ?? [] as $session)
                                <option value="{{ $session->id }}">{{ $session->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Date Range (Attendance)</label>
                        <div class="flex gap-2">
                            <input type="date" name="date_from" class="flex-1 px-3 py-2.5 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700 text-sm transition-all duration-200" placeholder="From">
                            <input type="date" name="date_to" class="flex-1 px-3 py-2.5 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50/50 text-slate-700 text-sm transition-all duration-200" placeholder="To">
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                    <span>📄</span> Generate Complete Report
                </button>
            </form>
        </div>

        <!-- Quick Access -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-2xl">⚡</div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Quick Access</h3>
                    <p class="text-sm text-slate-500">Recently viewed students</p>
                </div>
            </div>
            <div class="space-y-2 max-h-[320px] overflow-y-auto pr-1 custom-scroll">
                @forelse(($students ?? [])->take(8) as $student)
                    <a href="{{ route('admin.reports.student.generate', ['student_id' => $student->id]) }}" 
                       class="flex items-center justify-between p-3 rounded-xl hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all duration-200 group">
                        <div>
                            <p class="font-medium text-slate-700 text-sm group-hover:text-indigo-700 transition-colors">{{ $student->full_name }}</p>
                            <p class="text-xs text-slate-400">{{ $student->admission_number }} · {{ $student->current_class }}</p>
                        </div>
                        <span class="text-indigo-400 opacity-0 group-hover:opacity-100 transition-all duration-200 group-hover:translate-x-1">→</span>
                    </a>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <div class="text-3xl mb-2">👨‍🎓</div>
                        <p class="text-sm">No students available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===== QUICK LINKS ===== -->
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-lg">📌</span>
            <h3 class="text-sm font-semibold text-slate-700">Quick Report Types</h3>
            <span class="text-xs text-slate-400">(Direct links)</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.students.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform duration-300">👤</div>
                <p class="font-semibold text-slate-700 text-sm">Student List</p>
                <p class="text-xs text-slate-400">All students</p>
            </a>
            <a href="{{ route('admin.studentattendance.report') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform duration-300">📅</div>
                <p class="font-semibold text-slate-700 text-sm">Attendance Report</p>
                <p class="text-xs text-slate-400">Section wise</p>
            </a>
            <a href="{{ route('admin.exams.reports.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform duration-300">📊</div>
                <p class="font-semibold text-slate-700 text-sm">Exam Results</p>
                <p class="text-xs text-slate-400">Class & subject wise</p>
            </a>
            <a href="{{ route('admin.fee-reports.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="text-3xl mb-2 group-hover:scale-110 transition-transform duration-300">💰</div>
                <p class="font-semibold text-slate-700 text-sm">Fee Report</p>
                <p class="text-xs text-slate-400">Collection & dues</p>
            </a>
        </div>
    </div>

    <!-- ===== RECENT ACTIVITY ===== -->
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="text-lg">🔄</span>
            <h3 class="text-sm font-semibold text-slate-700">Recent Activity</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Students -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">👨‍🎓</span>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Recent Students Added</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse(($students ?? [])->take(5) as $student)
                        <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-medium text-slate-700 text-sm">{{ $student->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $student->admission_number }}</p>
                            </div>
                            <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $student->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-sm">No recent activity</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Exam Results -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">📊</span>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Recent Exam Results</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @php
                        $recentResults = \App\Models\ExamResult::with(['student', 'exam'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($recentResults as $result)
                        @php
                            $scoreColor = $result->percentage >= 80 ? 'text-emerald-600' : ($result->percentage >= 60 ? 'text-amber-500' : 'text-red-500');
                            $bgColor = $result->percentage >= 80 ? 'bg-emerald-50' : ($result->percentage >= 60 ? 'bg-amber-50' : 'bg-red-50');
                        @endphp
                        <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-medium text-slate-700 text-sm">{{ $result->student->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-400">{{ $result->exam->name ?? 'N/A' }}</p>
                            </div>
                            <span class="text-xs font-bold {{ $scoreColor }} {{ $bgColor }} px-2.5 py-1 rounded-full">
                                {{ number_format($result->percentage, 1) }}%
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-sm">No recent results</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <div class="mt-8 text-center">
        <p class="text-xs text-slate-400">📊 Student Reports Dashboard v1.0</p>
        <p class="text-xs text-slate-400 mt-1">Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</div>

@push('styles')
<style>
    .custom-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .animate-pulse {
        animation: pulse 3s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
</style>
@endpush
@endsection