@php
    // Helper function to check if route exists
    function routeExists($routeName) {
        return \Illuminate\Support\Facades\Route::has($routeName);
    }
@endphp

<aside class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen sticky top-0 overflow-y-auto shadow-2xl">
    <!-- Logo -->
    <div class="p-5 border-b border-gray-700/50 flex items-center space-x-3 bg-gray-900/95 sticky top-0 z-10">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SchoolAdmin</h2>
            <p class="text-[10px] text-gray-500 tracking-wider uppercase">Management System</p>
        </div>
    </div>

    <nav class="mt-4 px-3 space-y-1 pb-28" style="max-height: calc(100vh - 180px); overflow-y-auto;">

        <!-- ============================================================ -->
        <!-- DASHBOARD -->
        <!-- ============================================================ -->
        @if(routeExists('admin.dashboard'))
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- ACADEMICS -->
        <!-- ============================================================ -->
        @if(routeExists('admin.sessions.index') || routeExists('admin.classes.index') || routeExists('admin.grades.index') || routeExists('admin.streams.index') || routeExists('admin.class-sections.index') || routeExists('admin.students.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Academics</div>
        </div>
        @endif

        @if(routeExists('admin.sessions.index'))
        <a href="{{ route('admin.sessions.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sessions.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Academic Sessions
        </a>
        @endif

        @if(routeExists('admin.classes.index'))
        <a href="{{ route('admin.classes.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.classes.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Classes
        </a>
        @endif

        @if(routeExists('admin.grades.index'))
        <a href="{{ route('admin.grades.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.grades.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Grades
        </a>
        @endif

        @if(routeExists('admin.streams.index'))
        <a href="{{ route('admin.streams.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.streams.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Streams
        </a>
        @endif

        @if(routeExists('admin.class-sections.index'))
        <a href="{{ route('admin.class-sections.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.class-sections.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            Class Sections
        </a>
        @endif

        @if(routeExists('admin.students.index'))
        <a href="{{ route('admin.students.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Students
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- STUDENT REPORTS -->
        <!-- ============================================================ -->
        @if(routeExists('admin.reports.student.dashboard') || routeExists('admin.student-report-card.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Student Reports</div>
        </div>
        @endif

        @if(routeExists('admin.reports.student.dashboard'))
        <a href="{{ route('admin.reports.student.dashboard') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.student.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Student Reports
        </a>
        @endif

        @if(routeExists('admin.student-report-card.index'))
        <a href="{{ route('admin.student-report-card.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.student-report-card.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Student Report Card
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- TRANSFERS & CERTIFICATES -->
        <!-- ============================================================ -->
        @if(routeExists('admin.transfers.index') || routeExists('admin.transfer-certificates.index') || routeExists('admin.certificates.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Transfers & Certificates</div>
        </div>
        @endif

        @if(routeExists('admin.transfers.index'))
        <a href="{{ route('admin.transfers.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.transfers.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Student Transfers
        </a>
        @endif

        @if(routeExists('admin.transfer-certificates.index'))
        <a href="{{ route('admin.transfer-certificates.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.transfer-certificates.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Transfer Certificates
        </a>
        @endif

        @if(routeExists('admin.certificates.index'))
        <a href="{{ route('admin.certificates.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.certificates.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Certificates
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- ATTENDANCE -->
        <!-- ============================================================ -->
        @if(routeExists('admin.studentattendance.index') || routeExists('admin.attendance.index') || routeExists('admin.attendance.check') || routeExists('admin.teacher-attendance.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Attendance</div>
        </div>
        @endif

        <!-- Student Attendance Dropdown -->
        @if(routeExists('admin.studentattendance.index'))
        <div x-data="{ openStudentAtt: {{ request()->routeIs('admin.studentattendance.*') ? 'true' : 'false' }} }">
            <button @click="openStudentAtt = !openStudentAtt"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.studentattendance.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Student Attendance</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openStudentAtt }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openStudentAtt"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                
                @if(routeExists('admin.studentattendance.index'))
                <a href="{{ route('admin.studentattendance.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📊 Attendance Dashboard
                </a>
                @endif
                
                @if(routeExists('admin.studentattendance.create'))
                <a href="{{ route('admin.studentattendance.create') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.create') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    ✏️ Mark Attendance
                </a>
                @endif
                
                @if(routeExists('admin.studentattendance.report'))
                <a href="{{ route('admin.studentattendance.report') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.report') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📈 Attendance Report
                </a>
                @endif
                
                @if(routeExists('admin.studentattendance.section-wise-report'))
                <a href="{{ route('admin.studentattendance.section-wise-report') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.section-wise-report') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📊 Section-wise Report
                </a>
                @endif
                
                @if(routeExists('admin.studentattendance.pending-leaves'))
                <a href="{{ route('admin.studentattendance.pending-leaves') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.pending-leaves') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    ⏳ Pending Leave Requests
                </a>
                @endif
                
                @if(routeExists('admin.studentattendance.permissions'))
                <a href="{{ route('admin.studentattendance.permissions') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.permissions') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    👨‍🏫 Teacher Permissions
                </a>
                @endif
                
                @if(routeExists('admin.studentattendance.common-classes'))
                <a href="{{ route('admin.studentattendance.common-classes') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.common-classes') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📚 Common Subject Classes
                </a>
                @endif
            </div>
        </div>
        @endif

        @if(routeExists('admin.attendance.index'))
        <a href="{{ route('admin.attendance.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.attendance.index') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Staff Attendance
        </a>
        @endif

        @if(routeExists('admin.attendance.check'))
        <a href="{{ route('admin.attendance.check') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.attendance.check') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Check In/Out
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- TEACHER ATTENDANCE DROPDOWN -->
        <!-- ============================================================ -->
        @if(routeExists('admin.teacher-attendance.index'))
        <div x-data="{ openTeacherAtt: {{ request()->routeIs('admin.teacher-attendance.*') ? 'true' : 'false' }} }">
            <button @click="openTeacherAtt = !openTeacherAtt" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.teacher-attendance.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Teacher Attendance</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openTeacherAtt }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openTeacherAtt"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                
                @if(routeExists('admin.teacher-attendance.index'))
                <a href="{{ route('admin.teacher-attendance.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📋 Mark Attendance
                </a>
                @endif
                
                @if(routeExists('admin.teacher-attendance.class-wise'))
                <a href="{{ route('admin.teacher-attendance.class-wise') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.class-wise') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📊 Class Wise Report
                </a>
                @endif
                
                @if(routeExists('admin.teacher-attendance.per-class'))
                <a href="{{ route('admin.teacher-attendance.per-class') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.per-class') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📚 Per Class Report
                </a>
                @endif
                
                @if(routeExists('admin.teacher-attendance.arrival-departure'))
                <a href="{{ route('admin.teacher-attendance.arrival-departure') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.arrival-departure') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    🕐 Arrival/Departure
                </a>
                @endif
                
                @if(routeExists('admin.teacher-attendance.summary'))
                <a href="{{ route('admin.teacher-attendance.summary') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.summary') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📈 Summary Report
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- ============================================================ -->
        <!-- STAFF & HR -->
        <!-- ============================================================ -->
        @if(routeExists('admin.staff.index') || routeExists('admin.employee-categories.index') || routeExists('admin.teacher-availability.index') || routeExists('admin.leaves.index') || routeExists('admin.leaves.my'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Staff & HR</div>
        </div>
        @endif

        @if(routeExists('admin.staff.index'))
        <a href="{{ route('admin.staff.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Staff / Teachers
        </a>
        @endif

        @if(routeExists('admin.employee-categories.index'))
        <a href="{{ route('admin.employee-categories.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.employee-categories.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
            </svg>
            Staff Categories
        </a>
        @endif

        @if(routeExists('admin.teacher-availability.index'))
        <a href="{{ route('admin.teacher-availability.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.teacher-availability.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Teacher Availability
        </a>
        @endif

        @if(routeExists('admin.leaves.index'))
        <a href="{{ route('admin.leaves.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.leaves.index') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Leave Requests
        </a>
        @endif

        @if(routeExists('admin.leaves.my'))
        <a href="{{ route('admin.leaves.my') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.leaves.my') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            My Leaves
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- SALARY -->
        <!-- ============================================================ -->
        @if(routeExists('admin.salaries.index') || routeExists('admin.salaries.templates'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Salary</div>
        </div>
        @endif

        @if(routeExists('admin.salaries.index'))
        <a href="{{ route('admin.salaries.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.salaries.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Salary Dashboard
        </a>
        @endif

        @if(routeExists('admin.salaries.templates'))
        <a href="{{ route('admin.salaries.templates') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.salaries.templates') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Salary Templates
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- FEES MANAGEMENT -->
        <!-- ============================================================ -->
        @if(routeExists('admin.fee-types.index') || routeExists('admin.fee-submissions.index') || routeExists('admin.fee-installments.index') || routeExists('admin.invoices.index') || routeExists('admin.banks.index') || routeExists('admin.discounts.index') || routeExists('admin.discount-assignments.index') || routeExists('admin.fee-reports.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fees Management</div>
        </div>
        @endif

        @if(routeExists('admin.fee-types.index') || routeExists('admin.fee-submissions.index') || routeExists('admin.fee-installments.index') || routeExists('admin.invoices.index') || routeExists('admin.banks.index') || routeExists('admin.discounts.index') || routeExists('admin.discount-assignments.index') || routeExists('admin.fee-reports.index'))
        <div x-data="{ openFees: {{ request()->routeIs('admin.fee-types.*','admin.fee-submissions.*','admin.fee-installments.*','admin.invoices.*','admin.banks.*','admin.discounts.*','admin.discount-assignments.*','admin.fee-reports.*') ? 'true' : 'false' }} }">
            <button @click="openFees = !openFees"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fee-types.*','admin.fee-submissions.*','admin.fee-installments.*','admin.invoices.*','admin.banks.*','admin.discounts.*','admin.discount-assignments.*','admin.fee-reports.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Fees Management</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openFees }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openFees"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                
                @if(routeExists('admin.fee-types.index'))
                <a href="{{ route('admin.fee-types.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📋 Fee Types
                </a>
                @endif
                
                @if(routeExists('admin.fee-submissions.index'))
                <a href="{{ route('admin.fee-submissions.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-submissions.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📊 Fee Submissions
                </a>
                @endif
                
                @if(routeExists('admin.fee-installments.index'))
                <a href="{{ route('admin.fee-installments.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-installments.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📅 Installments
                </a>
                @endif
                
                @if(routeExists('admin.invoices.index'))
                <a href="{{ route('admin.invoices.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.invoices.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📄 Invoices
                </a>
                @endif
                
                @if(routeExists('admin.banks.index'))
                <a href="{{ route('admin.banks.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.banks.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    🏦 Banks
                </a>
                @endif
                
                @if(routeExists('admin.discounts.index'))
                <a href="{{ route('admin.discounts.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.discounts.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    🏷️ Discounts
                </a>
                @endif
                
                @if(routeExists('admin.discount-assignments.index'))
                <a href="{{ route('admin.discount-assignments.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.discount-assignments.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    👨‍🎓 Student Discounts
                </a>
                @endif
                
                @if(routeExists('admin.fee-reports.index'))
                <a href="{{ route('admin.fee-reports.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-reports.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📈 Fee Reports
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- ============================================================ -->
        <!-- EXAMINATION -->
        <!-- ============================================================ -->
        @if(routeExists('admin.exam-types.index') || routeExists('admin.exam-groups.index') || routeExists('admin.grade-scales.index') || routeExists('admin.exams.index') || routeExists('admin.exam-marks.index') || routeExists('admin.exams.reports.index') || routeExists('admin.results.class-wise') || routeExists('admin.exam-results.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Examination</div>
        </div>
        @endif

        @if(routeExists('admin.exam-types.index') || routeExists('admin.exam-groups.index') || routeExists('admin.grade-scales.index') || routeExists('admin.exams.index') || routeExists('admin.exam-marks.index') || routeExists('admin.exams.reports.index') || routeExists('admin.results.class-wise') || routeExists('admin.exam-results.index'))
        <div x-data="{ openExam: {{ request()->routeIs('admin.exam-types.*','admin.exam-groups.*','admin.grade-scales.*','admin.exams.*','admin.exam-marks.*','admin.exams.reports.*','admin.results.*','admin.exam-results.*') ? 'true' : 'false' }} }">
            <button @click="openExam = !openExam"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exam-types.*','admin.exam-groups.*','admin.grade-scales.*','admin.exams.*','admin.exam-marks.*','admin.exams.reports.*','admin.results.*','admin.exam-results.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Examination</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openExam }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="openExam"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                
                <!-- Exam Setup -->
                @if(routeExists('admin.exam-types.index') || routeExists('admin.exam-groups.index') || routeExists('admin.grade-scales.index'))
                <div x-data="{ openSetup: {{ request()->routeIs('admin.exam-types.*','admin.exam-groups.*','admin.grade-scales.*') ? 'true' : 'false' }} }">
                    <button @click="openSetup = !openSetup" class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">
                        <span>⚙️ Exam Setup</span>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': openSetup }" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openSetup" class="ml-4 mt-1 space-y-1 border-l border-gray-700/50">
                        @if(routeExists('admin.exam-types.index'))
                        <a href="{{ route('admin.exam-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📋 Exam Types
                        </a>
                        @endif
                        @if(routeExists('admin.exam-groups.index'))
                        <a href="{{ route('admin.exam-groups.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-groups.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📁 Exam Groups
                        </a>
                        @endif
                        @if(routeExists('admin.grade-scales.index'))
                        <a href="{{ route('admin.grade-scales.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.grade-scales.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📈 Grade Scales
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Exams -->
                @if(routeExists('admin.exams.index'))
                <a href="{{ route('admin.exams.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📋 Exam Dashboard
                </a>
                @endif
                
                @if(routeExists('admin.exams.create'))
                <a href="{{ route('admin.exams.create') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.create') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    ➕ Create Exam
                </a>
                @endif

                <!-- Marks Entry -->
                @if(routeExists('admin.exam-marks.index') || routeExists('admin.exam-marks.create') || routeExists('admin.exam-marks.bulk-upload'))
                <div x-data="{ openMarks: {{ request()->routeIs('admin.exam-marks.*') ? 'true' : 'false' }} }">
                    <button @click="openMarks = !openMarks" class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">
                        <span>✏️ Marks Entry</span>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': openMarks }" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openMarks" class="ml-4 mt-1 space-y-1 border-l border-gray-700/50">
                        @if(routeExists('admin.exam-marks.index'))
                        <a href="{{ route('admin.exam-marks.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-marks.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📊 Marks Dashboard
                        </a>
                        @endif
                        @if(routeExists('admin.exam-marks.create'))
                        <a href="{{ route('admin.exam-marks.create') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-marks.create') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            ➕ Enter Marks
                        </a>
                        @endif
                        @if(routeExists('admin.exam-marks.bulk-upload'))
                        <a href="{{ route('admin.exam-marks.bulk-upload') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-marks.bulk-upload') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📤 Bulk Upload
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Reports -->
                @if(routeExists('admin.exams.reports.index') || routeExists('admin.exams.reports.class-wise-form') || routeExists('admin.exams.reports.student-wise-form') || routeExists('admin.exams.reports.subject-wise-form') || routeExists('admin.exams.reports.performance-analysis') || routeExists('admin.exams.reports.grade-distribution-form'))
                <div x-data="{ openReports: {{ request()->routeIs('admin.exams.reports.*') ? 'true' : 'false' }} }">
                    <button @click="openReports = !openReports" class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">
                        <span>📊 Reports</span>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': openReports }" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openReports" class="ml-4 mt-1 space-y-1 border-l border-gray-700/50">
                        @if(routeExists('admin.exams.reports.index'))
                        <a href="{{ route('admin.exams.reports.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📊 Reports Dashboard
                        </a>
                        @endif
                        @if(routeExists('admin.exams.reports.class-wise-form'))
                        <a href="{{ route('admin.exams.reports.class-wise-form') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.class-wise*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            🏫 Class-wise Report
                        </a>
                        @endif
                        @if(routeExists('admin.exams.reports.student-wise-form'))
                        <a href="{{ route('admin.exams.reports.student-wise-form') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.student-wise*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            👨‍🎓 Student-wise Report
                        </a>
                        @endif
                        @if(routeExists('admin.exams.reports.subject-wise-form'))
                        <a href="{{ route('admin.exams.reports.subject-wise-form') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.subject-wise*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📖 Subject-wise Report
                        </a>
                        @endif
                        @if(routeExists('admin.exams.reports.performance-analysis'))
                        <a href="{{ route('admin.exams.reports.performance-analysis') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.performance-analysis') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📈 Performance Analysis
                        </a>
                        @endif
                        @if(routeExists('admin.exams.reports.grade-distribution-form'))
                        <a href="{{ route('admin.exams.reports.grade-distribution-form') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.grade-distribution*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📊 Grade Distribution
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Results -->
                @if(routeExists('admin.results.class-wise'))
                <a href="{{ route('admin.results.class-wise') }}" 
                   class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.results.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                    📋 Results
                </a>
                @endif

                <!-- Results Analysis -->
                @if(routeExists('admin.exam-results.index') || routeExists('admin.exam-results.student-wise') || routeExists('admin.exam-results.subject-wise') || routeExists('admin.exam-results.section-wise') || routeExists('admin.exam-results.compare-tests'))
                <div x-data="{ openResults: {{ request()->routeIs('admin.exam-results.*') ? 'true' : 'false' }} }">
                    <button @click="openResults = !openResults" class="w-full flex items-center justify-between px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">
                        <span>📊 Results Analysis</span>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': openResults }" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openResults" class="ml-4 mt-1 space-y-1 border-l border-gray-700/50">
                        @if(routeExists('admin.exam-results.student-wise'))
                        <a href="{{ route('admin.exam-results.student-wise') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-results.student-wise') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            👨‍🎓 Student Wise
                        </a>
                        @endif
                        @if(routeExists('admin.exam-results.subject-wise'))
                        <a href="{{ route('admin.exam-results.subject-wise') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-results.subject-wise') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📖 Subject Wise
                        </a>
                        @endif
                        @if(routeExists('admin.exam-results.section-wise'))
                        <a href="{{ route('admin.exam-results.section-wise') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-results.section-wise') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            🏫 Section Wise
                        </a>
                        @endif
                        @if(routeExists('admin.exam-results.compare-tests'))
                        <a href="{{ route('admin.exam-results.compare-tests') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-results.compare-tests') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📊 Compare Tests
                        </a>
                        @endif
                        @if(routeExists('admin.exam-results.index'))
                        <a href="{{ route('admin.exam-results.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-results.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                            📊 Dashboard
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- ============================================================ -->
        <!-- CURRICULUM -->
        <!-- ============================================================ -->
        @if(routeExists('admin.subjects.index') || routeExists('admin.subject-assignments.index') || routeExists('admin.class-subject.index'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Curriculum</div>
        </div>
        @endif

        @if(routeExists('admin.subjects.index'))
        <a href="{{ route('admin.subjects.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Subjects
        </a>
        @endif

        @if(routeExists('admin.subject-assignments.index'))
        <a href="{{ route('admin.subject-assignments.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.subject-assignments.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Subject Assignments
        </a>
        @endif

        @if(routeExists('admin.class-subject.index'))
        <a href="{{ route('admin.class-subject.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.class-subject.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Section-wise Subjects
        </a>
        @endif

        <!-- ============================================================ -->
        <!-- TIMETABLE -->
        <!-- ============================================================ -->
        @if(routeExists('admin.school-timings.index') || routeExists('admin.timetable-reports.index') || routeExists('admin.timetable-reports.class') || routeExists('admin.timetable-reports.section') || routeExists('admin.timetable-reports.teacher'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Timetable</div>
        </div>
        @endif

        @if(routeExists('admin.school-timings.index'))
        <a href="{{ route('admin.school-timings.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.school-timings.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            School Timings
        </a>
        @endif        @if(routeExists('admin.timetable-reports.index'))
        <a href="{{ route('admin.timetable-reports.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.timetable-reports.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Timetable Dashboard
        </a>
        @endif

        <!-- Timetable Sub-links -->
        @if(routeExists('admin.timetable-reports.class') || routeExists('admin.timetable-reports.section') || routeExists('admin.timetable-reports.teacher'))
        <div class="ml-6 mt-1 space-y-1 border-l border-gray-700/50">
            @if(routeExists('admin.timetable-reports.class'))
            <a href="{{ route('admin.timetable-reports.class') }}" 
               class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.timetable-reports.class') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                📚 Class View
            </a>
            @endif
            @if(routeExists('admin.timetable-reports.section'))
            <a href="{{ route('admin.timetable-reports.section') }}" 
               class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.timetable-reports.section') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                📋 Section View
            </a>
            @endif
            @if(routeExists('admin.timetable-reports.teacher'))
            <a href="{{ route('admin.timetable-reports.teacher') }}" 
               class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.timetable-reports.teacher') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">
                👨‍🏫 Teacher View
            </a>
            @endif
        </div>
        @endif

        <!-- ============================================================ -->
        <!-- INFRASTRUCTURE -->
        <!-- ============================================================ -->
        @if(routeExists('admin.blocks.index') || routeExists('admin.floors.index') || routeExists('admin.rooms.index') || routeExists('admin.rooms.assignments'))
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Infrastructure</div>
        </div>
        @endif

        @if(routeExists('admin.blocks.index'))
        <a href="{{ route('admin.blocks.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.blocks.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Blocks
        </a>
        @endif

        @if(routeExists('admin.floors.index'))
        <a href="{{ route('admin.floors.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.floors.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Floors
        </a>
        @endif

        @if(routeExists('admin.rooms.index'))
        <a href="{{ route('admin.rooms.index') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.rooms.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Rooms
        </a>
        @endif

        @if(routeExists('admin.rooms.assignments'))
        <a href="{{ route('admin.rooms.assignments') }}"
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.rooms.assignments') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Room Assignments
        </a>
        @endif

     <!-- ============================================================ -->
<!-- TEACHER WISE RESULTS -->
<!-- ============================================================ -->
@if(routeExists('admin.exams.reports.teacher-wise'))
<div class="pt-3 mt-2 border-t border-gray-700/30">
    <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher Reports</div>
</div>

<a href="{{ route('admin.exams.reports.teacher-wise') }}"
   class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exams.reports.teacher-wise*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    Teacher Wise Results
</a>
@endif

    </nav>
</aside>

<script>
    document.addEventListener('alpine:init', function() {
        console.log('Alpine.js initialized successfully');
    });
</script>