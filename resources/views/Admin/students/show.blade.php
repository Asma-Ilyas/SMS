@extends('layouts.app')

@section('title', 'Student Profile - ' . $student->full_name)

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- ===== MODERN PROFILE HERO ===== -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-2xl p-6 md:p-8 mb-6 shadow-2xl shadow-indigo-500/30">
        <!-- Background Decorations -->
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/5 rounded-full animate-pulse"></div>
        <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-white/5 rounded-full animate-pulse delay-1000"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center gap-6">
            <!-- Avatar -->
            <div class="relative">
                @if($student->profile_photo)
                    <img src="{{ asset('storage/' . $student->profile_photo) }}" 
                         alt="{{ $student->full_name }}" 
                         class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white/40 shadow-xl object-cover hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white/40 bg-white/20 backdrop-blur-sm flex items-center justify-center text-3xl font-bold text-white shadow-xl">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </div>
                @endif
                <div class="absolute bottom-0 right-0 w-5 h-5 bg-green-400 rounded-full border-2 border-white animate-pulse"></div>
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $student->full_name }}</h1>
                <div class="flex flex-wrap items-center gap-2 text-white/80 text-sm">
                    <span class="flex items-center gap-1">🎓 {{ $student->admission_number }}</span>
                    <span class="hidden sm:inline">•</span>
                    <span class="flex items-center gap-1">🏫 {{ $student->current_class }}</span>
                    <span class="hidden sm:inline">•</span>
                    <span class="flex items-center gap-1">📋 Roll: {{ $student->roll_number ?? 'N/A' }}</span>
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/30 backdrop-blur-sm text-white text-xs font-medium rounded-full border border-white/20">
                        {{ $student->gender }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/30 backdrop-blur-sm text-white text-xs font-medium rounded-full border border-white/20">
                        🎂 {{ $student->age }} years
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-medium rounded-full border border-white/20">
                        {{ $student->status == 'Active' ? '✅ Active' : '❌ Inactive' }}
                    </span>
                </div>
            </div>

            <!-- Stats & Actions -->
            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <div class="flex gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                    <div class="text-center">
                        <div class="text-lg font-bold text-white">{{ number_format($stats['attendance_percentage'], 1) }}%</div>
                        <div class="text-xs text-white/70">Attendance</div>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-white">{{ $stats['grade'] }}</div>
                        <div class="text-xs text-white/70">Grade</div>
                    </div>
                    <div class="w-px bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-white">{{ $stats['total_exams'] }}</div>
                        <div class="text-xs text-white/70">Exams</div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.students.edit', $student->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl text-sm font-medium">
                        ✏️ Edit
                    </a>
                    <a href="{{ route('admin.students.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-medium">
                        ← Back
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm text-white hover:bg-white/30 rounded-xl transition-all duration-300 text-sm font-medium">
                        🖨️ Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== STATS GRID ===== -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
            <div class="text-2xl mb-1 text-center">📊</div>
            <p class="text-xs text-gray-500 font-medium text-center">Attendance</p>
            <p class="text-2xl font-bold text-center 
                {{ $stats['attendance_percentage'] >= 80 ? 'text-emerald-600' : ($stats['attendance_percentage'] >= 60 ? 'text-amber-500' : 'text-red-500') }}">
                {{ number_format($stats['attendance_percentage'], 1) }}%
            </p>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                <div class="h-1.5 rounded-full transition-all duration-1000 
                    {{ $stats['attendance_percentage'] >= 80 ? 'bg-emerald-500' : ($stats['attendance_percentage'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" 
                    style="width: {{ $stats['attendance_percentage'] }}%"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
            <div class="text-2xl mb-1 text-center">📈</div>
            <p class="text-xs text-gray-500 font-medium text-center">Average Marks</p>
            <p class="text-2xl font-bold text-center text-indigo-600">{{ number_format($stats['avg_percentage'], 1) }}%</p>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
            <div class="text-2xl mb-1 text-center">🏆</div>
            <p class="text-xs text-gray-500 font-medium text-center">Grade</p>
            <p class="text-2xl font-bold text-center text-purple-600">{{ $stats['grade'] }}</p>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
            <div class="text-2xl mb-1 text-center">📝</div>
            <p class="text-xs text-gray-500 font-medium text-center">Total Exams</p>
            <p class="text-2xl font-bold text-center text-gray-800">{{ $stats['total_exams'] }}</p>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100">
            <div class="text-2xl mb-1 text-center">💳</div>
            <p class="text-xs text-gray-500 font-medium text-center">Fee Status</p>
            <p class="text-2xl font-bold text-center {{ $stats['pending_fee'] > 0 ? 'text-red-500' : 'text-emerald-600' }}">
                {{ $stats['pending_fee'] > 0 ? '⚠️' : '✅' }}
            </p>
            <p class="text-xs text-gray-400 text-center mt-1">${{ number_format($stats['pending_fee'], 2) }} due</p>
        </div>
    </div>

    <!-- ===== PERSONAL INFORMATION ===== -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="text-xl">👤</span> Personal Information
                <span class="text-xs font-normal text-gray-400 ml-2 bg-gray-200 px-2 py-0.5 rounded-full">{{ $student->gender }}, {{ $student->age }} yrs</span>
            </h3>
            <a href="{{ route('admin.students.edit', $student->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition-all duration-300 hover:gap-2">
                ✏️ Edit
            </a>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>📋</span> Personal Details
                    </h4>
                    <div class="space-y-1">
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">📛 First Name</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->first_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">📛 Last Name</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->last_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🎂 Date of Birth</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">⚧️ Gender</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->gender }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🕊️ Religion</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->religion ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🩸 Blood Group</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->blood_group ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🗣️ Mother Tongue</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->mother_tongue ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">📍 Birth Place</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->birth_place ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>📞</span> Contact & Academic
                    </h4>
                    <div class="space-y-1">
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">📧 Email</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">📱 Phone</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🏠 Address</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->address ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🏙️ City</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->city ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🗺️ State</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->state ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🌍 Country</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->country ?? 'Pakistan' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">📅 Admission Date</span>
                            <span class="text-sm font-medium text-gray-800">{{ $student->admission_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-50 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🎯 Student Type</span>
                            <span class="text-sm font-medium text-gray-800">
                                <span class="inline-flex px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">{{ $student->student_type }}</span>
                            </span>
                        </div>
                        <div class="flex justify-between py-2 hover:bg-gray-50 px-2 rounded-lg transition-colors duration-200">
                            <span class="text-sm text-gray-500 flex items-center gap-1">🏫 Class</span>
                            <span class="text-sm font-medium text-gray-800">
                                <span class="inline-flex px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ $student->current_class }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== PARENTS ===== -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>👨‍👩‍👧</span> Parents Information
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-200 hover:border-indigo-300 transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl">👨</span>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Father</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $student->father_name }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                            <span class="flex items-center gap-1">💼 {{ $student->father_occupation ?? 'N/A' }}</span>
                            <span class="flex items-center gap-1">📞 {{ $student->father_phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-200 hover:border-indigo-300 transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl">👩</span>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Mother</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $student->mother_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                            <span class="flex items-center gap-1">💼 {{ $student->mother_occupation ?? 'N/A' }}</span>
                            <span class="flex items-center gap-1">📞 {{ $student->mother_phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== WEEKLY TIMETABLE ===== -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="text-xl">⏰</span> Weekly Timetable
                <span class="text-xs font-normal text-gray-400 ml-2">
                    {{ $student->current_class }} • Section {{ $student->classSection->section_name ?? 'N/A' }}
                </span>
            </h3>
            <a href="{{ route('admin.timetable-reports.by-section', ['classSectionId' => $student->class_section_id]) }}" 
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition-all duration-300 hover:gap-2">
                📋 View Full
            </a>
        </div>
        
        <div class="p-6">
            @php
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                $dayFull = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            @endphp

            @if($timetable->isNotEmpty() && $timeSlots->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 rounded-l-lg" style="min-width:80px;">
                                    Time
                                </th>
                                @foreach($dayLabels as $i => $label)
                                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                                        {{ $label }}
                                        <span class="block text-[10px] font-normal text-gray-400">{{ $dayFull[$i] }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slot)
                                <tr>
                                    <td class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-50 rounded-l-lg whitespace-nowrap text-center">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                        <span class="block text-[10px] font-normal text-gray-400">
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </span>
                                    </td>
                                    @foreach($days as $day)
                                        @php
                                            $entry = $timetable[$day] ?? collect();
                                            $entry = $entry->firstWhere('time_slot_id', $slot->id);
                                        @endphp
                                        <td class="px-1 py-1">
                                            @if($entry)
                                                <div class="bg-white border border-gray-200 rounded-lg p-2 hover:border-indigo-400 hover:shadow-md transition-all duration-300">
                                                    <div class="font-semibold text-gray-800 text-xs flex items-center justify-between">
                                                        {{ $entry->subject->name ?? 'N/A' }}
                                                        <span class="text-[10px] bg-gray-100 px-1.5 py-0.5 rounded-full text-gray-500">{{ $entry->subject->code ?? '' }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                                        👨‍🏫 {{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                                        🚪 {{ $entry->room->name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-300 text-sm">
                                                    −
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Timetable Summary -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
                    <div class="bg-indigo-50 rounded-xl p-3 text-center border border-indigo-100">
                        <p class="text-xs text-gray-500">Total Subjects</p>
                        <p class="text-xl font-bold text-indigo-600">
                            {{ $timetable->flatMap(function($items) { return $items; })->unique('subject_id')->count() }}
                        </p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-100">
                        <p class="text-xs text-gray-500">Periods/Week</p>
                        <p class="text-xl font-bold text-emerald-600">
                            {{ $timetable->flatMap(function($items) { return $items; })->count() }}
                        </p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-100">
                        <p class="text-xs text-gray-500">Teachers</p>
                        <p class="text-xl font-bold text-amber-600">
                            {{ $timetable->flatMap(function($items) { return $items; })->unique('teacher_id')->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3 text-center border border-purple-100">
                        <p class="text-xs text-gray-500">Section</p>
                        <p class="text-xl font-bold text-purple-600">
                            {{ $student->classSection->section_name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-5xl text-gray-300 mb-4">⏰</div>
                    <p class="text-gray-500 font-medium text-lg">No timetable assigned yet</p>
                    <p class="text-sm text-gray-400 mt-1">Please generate timetable or assign classes for this student.</p>
                    <a href="{{ route('admin.timetable-reports.generate') }}" class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-xl transition-all duration-300 shadow-sm">
                        ⚙️ Generate Timetable
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- ===== RECENT ATTENDANCE ===== -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="text-xl">📅</span> Recent Attendance
                <span class="text-xs font-normal text-gray-400 ml-2">Last 5 records</span>
            </h3>
            <a href="{{ route('admin.students.attendance', $student->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition-all duration-300 hover:gap-2">
                📊 View All
            </a>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 rounded-lg">
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentAttendance as $attendance)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($attendance->status == 'present')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">✅ Present</span>
                                    @elseif($attendance->status == 'absent')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">❌ Absent</span>
                                    @elseif($attendance->status == 'leave')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">📋 Leave</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->subject->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $attendance->teacher->full_name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">📭 No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== EXAM RESULTS ===== -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="text-xl">📊</span> Recent Exam Results
                <span class="text-xs font-normal text-gray-400 ml-2">Latest {{ $examPerformance->count() }} exams</span>
            </h3>
            <a href="{{ route('admin.students.exam-results', $student->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition-all duration-300 hover:gap-2">
                📈 View All
            </a>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 rounded-lg">
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($examPerformance as $result)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $result->exam->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ number_format($result->total_marks, 1) }}/{{ number_format($result->total_max_marks, 1) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold 
                                        {{ $result->percentage >= 80 ? 'text-emerald-600' : ($result->percentage >= 60 ? 'text-amber-500' : 'text-red-500') }}">
                                        {{ number_format($result->percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($result->rank_in_class)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                                            🏅 #{{ $result->rank_in_class }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">📭 No exam results available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== FEE SUMMARY ===== -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <span class="text-xl">💰</span> Fee Summary
            </h3>
            <a href="{{ route('admin.students.fee-details', $student->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition-all duration-300 hover:gap-2">
                💳 View Details
            </a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 text-center border border-indigo-200 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 font-medium">💰 Total Fee</p>
                    <p class="text-2xl font-bold text-indigo-600">${{ number_format($feeSummary['total'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 text-center border border-emerald-200 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 font-medium">✅ Paid</p>
                    <p class="text-2xl font-bold text-emerald-600">${{ number_format($feeSummary['paid'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 text-center border border-red-200 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 font-medium">⏳ Pending</p>
                    <p class="text-2xl font-bold text-red-500">${{ number_format($feeSummary['pending'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 text-center border border-amber-200 hover:shadow-md transition-all duration-300">
                    <p class="text-xs text-gray-500 font-medium">⚠️ Overdue</p>
                    <p class="text-2xl font-bold text-amber-500">{{ $feeSummary['overdue'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== QUICK ACTIONS ===== -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 no-print">
        <a href="{{ route('admin.students.edit', $student->id) }}" 
           class="bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group">
            <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform duration-300">✏️</span>
            <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-600">Edit Profile</span>
        </a>
        <a href="{{ route('admin.students.attendance', $student->id) }}" 
           class="bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group">
            <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform duration-300">📅</span>
            <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-600">Attendance</span>
        </a>
        <a href="{{ route('admin.students.exam-results', $student->id) }}" 
           class="bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group">
            <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform duration-300">📊</span>
            <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-600">Exam Results</span>
        </a>
        <a href="{{ route('admin.students.fee-details', $student->id) }}" 
           class="bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group">
            <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform duration-300">💰</span>
            <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-600">Fee Details</span>
        </a>
        <a href="{{ route('admin.students.index') }}" 
           class="bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group">
            <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform duration-300">←</span>
            <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-600">Back to List</span>
        </a>
        <button onclick="window.print()" 
                class="bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-xl p-4 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group">
            <span class="text-3xl block mb-1 group-hover:scale-110 transition-transform duration-300">🖨️</span>
            <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-600">Print Profile</span>
        </button>
    </div>

</div>

@push('styles')
<style>
    /* Print Styles */
    @media print {
        .no-print { display: none !important; }
        .container { max-width: 100% !important; padding: 0 !important; }
        .shadow-2xl, .shadow-sm, .shadow-lg { box-shadow: none !important; }
        .rounded-2xl { border-radius: 8px !important; }
        .bg-gradient-to-br { background: #f8fafc !important; }
        .border { border-color: #e2e8f0 !important; }
        .hover\:shadow-xl { box-shadow: none !important; }
        .hover\:-translate-y-1 { transform: none !important; }
        .bg-white { background: white !important; }
        .bg-gray-50 { background: #f8fafc !important; }
    }
    
    /* Animation Delays */
    .delay-1000 { animation-delay: 1000ms; }
    
    /* Smooth Transitions */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 8px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush
@endsection