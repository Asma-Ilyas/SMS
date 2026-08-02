@extends('layouts.app')

@section('title', 'Student Report Card')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl space-y-6">
    
    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 shadow-lg shadow-indigo-100">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="p-6 md:p-8 lg:p-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 flex items-center justify-center border border-white/10 shadow-inner">
                        <i class="fas fa-file-alt text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Student Report Card</h1>
                        <p class="text-white/80 text-sm mt-1 max-w-xl">Generate comprehensive report cards with marks, grades & attendance</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3 md:justify-end">
                    <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10 shadow-sm">
                        <i class="fas fa-users"></i>
                        <span>{{ $students->count() }} Students</span>
                    </span>
                    <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full text-white text-sm font-medium border border-white/10 shadow-sm">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $academicSessions->count() }} Sessions</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="p-6">
            <form method="POST" action="{{ route('admin.student-report-card.generate') }}" id="reportForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5 items-end">
                    
                    <div class="lg:col-span-4 space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            <i class="fas fa-user-graduate text-indigo-500 mr-1.5"></i>
                            Select Student <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="student_id" id="student_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors" required>
                                <option value="">🔍 Search for a student...</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                        ({{ $student->admission_number ?? 'N/A' }})
                                        - {{ $student->classSection->class->grade->name ?? 'N/A' }}
                                        {{ $student->classSection->section_name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="lg:col-span-3 space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            <i class="fas fa-calendar-alt text-indigo-500 mr-1.5"></i>
                            Academic Session
                        </label>
                        <select name="session_id" id="session_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                            <option value="">📚 All Sessions</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->id }}" {{ $session->is_active ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-3 space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            <i class="fas fa-book text-indigo-500 mr-1.5"></i>
                            Specific Exam
                        </label>
                        <select name="exam_id" id="exam_id" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                            <option value="">📖 All Exams</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">
                                    {{ $exam->name }} @if($exam->examType)({{ $exam->examType->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl px-4 py-3 shadow-md shadow-indigo-100 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200">
                            <i class="fas fa-file-alt"></i>
                            <span>Generate</span>
                        </button>
                        <button type="button" onclick="resetForm()" class="inline-flex items-center justify-center bg-white border border-slate-200 text-slate-500 rounded-xl p-3 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>

                <!-- Date Range for Attendance -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-calendar-check text-indigo-500"></i>
                        <h6 class="font-semibold text-slate-700 text-sm">Attendance Date Range <span class="text-slate-400 font-normal">(Optional)</span></h6>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <i class="fas fa-calendar-day text-indigo-400 mr-1"></i>
                                From Date
                            </label>
                            <input type="date" name="date_from" id="date_from" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors" 
                                   value="{{ old('date_from', date('Y-m-01')) }}">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <i class="fas fa-calendar-day text-indigo-400 mr-1"></i>
                                To Date
                            </label>
                            <input type="date" name="date_to" id="date_to" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors" 
                                   value="{{ old('date_to', date('Y-m-d')) }}">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="button" onclick="setDateRange('month')" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold rounded-xl px-3 py-2.5 hover:bg-indigo-100 transition-colors border border-indigo-100">
                                <i class="fas fa-calendar-alt"></i>
                                This Month
                            </button>
                            <button type="button" onclick="setDateRange('year')" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold rounded-xl px-3 py-2.5 hover:bg-indigo-100 transition-colors border border-indigo-100">
                                <i class="fas fa-calendar-alt"></i>
                                This Year
                            </button>
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="clearDateRange()" class="w-full inline-flex items-center justify-center gap-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl px-3 py-2.5 hover:bg-slate-200 transition-colors border border-slate-200">
                                <i class="fas fa-times"></i>
                                Clear Range
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="group bg-white rounded-2xl border border-slate-100 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Total Students</p>
                    <h3 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $students->count() }}</h3>
                </div>
                <div class="bg-indigo-50 text-indigo-600 rounded-xl p-3.5 transition-colors group-hover:bg-indigo-100">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
            </div>
        </div>

        <div class="group bg-white rounded-2xl border border-slate-100 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Active Students</p>
                    <h3 class="text-2xl font-bold text-emerald-600 tracking-tight">{{ $students->where('status', 'Active')->count() }}</h3>
                </div>
                <div class="bg-emerald-50 text-emerald-600 rounded-xl p-3.5 transition-colors group-hover:bg-emerald-100">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
            </div>
        </div>

        <div class="group bg-white rounded-2xl border border-slate-100 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Academic Sessions</p>
                    <h3 class="text-2xl font-bold text-amber-500 tracking-tight">{{ $academicSessions->count() }}</h3>
                </div>
                <div class="bg-amber-50 text-amber-600 rounded-xl p-3.5 transition-colors group-hover:bg-amber-100">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Total</span>
            </div>
        </div>

        <div class="group bg-white rounded-2xl border border-slate-100 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Published Exams</p>
                    <h3 class="text-2xl font-bold text-rose-500 tracking-tight">{{ $exams->count() }}</h3>
                </div>
                <div class="bg-rose-50 text-rose-600 rounded-xl p-3.5 transition-colors group-hover:bg-rose-100">
                    <i class="fas fa-book text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-100">Published</span>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="bg-indigo-50/60 rounded-2xl border border-indigo-100/80 p-5 md:p-6">
        <div class="flex items-start gap-4">
            <div class="bg-indigo-600 text-white rounded-xl p-3 flex items-center justify-center shadow-md shadow-indigo-100">
                <i class="fas fa-lightbulb text-xl"></i>
            </div>
            <div class="space-y-2">
                <h5 class="text-base font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fas fa-rocket text-indigo-500 text-sm"></i>
                    How to Generate a Report Card
                </h5>
                <div class="text-sm text-slate-600 leading-relaxed space-y-2 lg:space-y-0 lg:flex lg:flex-wrap lg:items-center lg:gap-x-4">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 shadow-sm">1</span>
                        <span>Select a student from the dropdown menu above.</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 shadow-sm">2</span>
                        <span class="text-slate-500">(Optional) Filter down by academic session or target exam.</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 shadow-sm">3</span>
                        <span>Set a date range for attendance filtering or use quick buttons.</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center justify-center bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 shadow-sm">4</span>
                        <span>Click <strong class="text-indigo-700 font-semibold">"Generate"</strong> to load and build the dynamic analytics interface.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#student_id').select2({
        theme: 'bootstrap4',
        placeholder: '🔍 Search for a student...',
        allowClear: true,
        width: '100%'
    });

    $('#session_id, #exam_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select option',
        allowClear: true,
        width: '100%'
    });

    $('#student_id').on('change', function() {
        if ($(this).val()) {
            $('#reportForm').submit();
        }
    });
});

function setDateRange(type) {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    
    if (type === 'month') {
        document.getElementById('date_from').value = year + '-' + month + '-01';
        document.getElementById('date_to').value = year + '-' + month + '-' + day;
    } else if (type === 'year') {
        document.getElementById('date_from').value = year + '-01-01';
        document.getElementById('date_to').value = year + '-' + month + '-' + day;
    }
}

function clearDateRange() {
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
}

function resetForm() {
    window.location.href = "{{ route('admin.student-report-card.index') }}";
}
</script>
@endpush
@endsection