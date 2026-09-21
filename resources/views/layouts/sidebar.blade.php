@php
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
            <h2 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SCHOOL</h2>
            <p class="text-[10px] text-gray-500 tracking-wider uppercase">Management System</p>
        </div>
    </div>

    <nav class="mt-4 px-3 space-y-1 pb-28" style="max-height: calc(100vh - 180px); overflow-y: auto;">

        {{-- ============================================================ --}}
        {{-- DASHBOARD                                                   --}}
        {{-- ============================================================ --}}
        @role('admin')
            @if(routeExists('admin.dashboard'))
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            @endif
        @endrole

        @role('teacher')
            @if(routeExists('teacher.dashboard'))
            <a href="{{ route('teacher.dashboard') }}"
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            @endif
        @endrole

        @role('student')
            @if(routeExists('student.dashboard'))
            <a href="{{ route('student.dashboard') }}"
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('student.dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            @endif
        @endrole

        {{-- ============================================================ --}}
        {{-- NOTIFICATIONS — visible to every logged-in role             --}}
        {{-- ============================================================ --}}
        @auth
            @php $unreadNotifications = auth()->user()->unreadNotifications()->count(); @endphp
            <a href="{{ route('notifications.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('notifications.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notifications
                </span>
                @if($unreadNotifications > 0)
                    <span class="ml-2 rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                @endif
            </a>
        @endauth

        {{-- ============================================================ --}}
        {{-- ADMIN SECTIONS                                              --}}
        {{-- ============================================================ --}}
        @role('admin')

            {{-- USER MANAGEMENT --}}
            @if(routeExists('admin.users.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administration</div>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                User Management
            </a>
            @endif

            {{-- ANNOUNCEMENTS --}}
            @if(routeExists('admin.announcements.index'))
            <a href="{{ route('admin.announcements.index') }}"
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.announcements.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                Announcements
            </a>
            @endif

            {{-- ACADEMICS --}}
            @if(routeExists('admin.sessions.index') || routeExists('admin.classes.index') || routeExists('admin.grades.index') || routeExists('admin.streams.index') || routeExists('admin.class-sections.index'))
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

            {{-- STUDENTS --}}
            @if(routeExists('admin.students.index'))
            <a href="{{ route('admin.students.index') }}"
               class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Students
            </a>
            @endif

            {{-- STUDENT REPORTS --}}
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

            {{-- TRANSFERS & CERTIFICATES --}}
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

            {{-- ATTENDANCE (admin full) --}}
            @if(routeExists('admin.studentattendance.index') || routeExists('admin.attendance.index') || routeExists('admin.attendance.check') || routeExists('admin.teacher-attendance.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Attendance</div>
            </div>
            @endif

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
                <div x-show="openStudentAtt" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0" class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                    @if(routeExists('admin.studentattendance.index'))<a href="{{ route('admin.studentattendance.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Attendance Dashboard</a>@endif
                    @if(routeExists('admin.studentattendance.create'))<a href="{{ route('admin.studentattendance.create') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.create') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">✏️ Mark Attendance</a>@endif
                    @if(routeExists('admin.studentattendance.report'))<a href="{{ route('admin.studentattendance.report') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.report') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📈 Attendance Report</a>@endif
                    @if(routeExists('admin.studentattendance.section-wise-report'))<a href="{{ route('admin.studentattendance.section-wise-report') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.section-wise-report') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Section-wise Report</a>@endif
                    @if(routeExists('admin.studentattendance.pending-leaves'))<a href="{{ route('admin.studentattendance.pending-leaves') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.pending-leaves') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">⏳ Pending Leave Requests</a>@endif
                    @if(routeExists('admin.studentattendance.permissions'))<a href="{{ route('admin.studentattendance.permissions') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.permissions') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">👨‍🏫 Teacher Permissions</a>@endif
                    @if(routeExists('admin.studentattendance.common-classes'))<a href="{{ route('admin.studentattendance.common-classes') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.studentattendance.common-classes') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📚 Common Subject Classes</a>@endif
                </div>
            </div>
            @endif

            @if(routeExists('admin.attendance.index'))
            <a href="{{ route('admin.attendance.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.attendance.index') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Staff Attendance
            </a>
            @endif

            @if(routeExists('admin.attendance.check'))
            <a href="{{ route('admin.attendance.check') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.attendance.check') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Check In/Out
            </a>
            @endif

            @if(routeExists('admin.teacher-attendance.index'))
            <div x-data="{ openTeacherAtt: {{ request()->routeIs('admin.teacher-attendance.*') ? 'true' : 'false' }} }">
                <button @click="openTeacherAtt = !openTeacherAtt" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.teacher-attendance.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Teacher Attendance</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openTeacherAtt }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openTeacherAtt" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0" class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                    @if(routeExists('admin.teacher-attendance.index'))<a href="{{ route('admin.teacher-attendance.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📋 Mark Attendance</a>@endif
                    @if(routeExists('admin.teacher-attendance.class-wise'))<a href="{{ route('admin.teacher-attendance.class-wise') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.class-wise') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Class Wise</a>@endif
                    @if(routeExists('admin.teacher-attendance.per-class'))<a href="{{ route('admin.teacher-attendance.per-class') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.per-class') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📚 Per Class Report</a>@endif
                    @if(routeExists('admin.teacher-attendance.arrival-departure'))<a href="{{ route('admin.teacher-attendance.arrival-departure') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.arrival-departure') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🕐 Arrival/Departure</a>@endif
                    @if(routeExists('admin.teacher-attendance.summary'))<a href="{{ route('admin.teacher-attendance.summary') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.teacher-attendance.summary') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📈 Summary Report</a>@endif
                </div>
            </div>
            @endif

            {{-- STAFF & HR --}}
            @if(routeExists('admin.staff.index') || routeExists('admin.employee-categories.index') || routeExists('admin.teacher-availability.index') || routeExists('admin.leaves.index') || routeExists('admin.leaves.my'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Staff & HR</div>
            </div>
            @endif

            @if(routeExists('admin.staff.index'))
            <a href="{{ route('admin.staff.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Staff / Teachers
            </a>
            @endif

            @if(routeExists('admin.employee-categories.index'))
            <a href="{{ route('admin.employee-categories.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.employee-categories.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                Staff Categories
            </a>
            @endif

            @if(routeExists('admin.teacher-availability.index'))
            <a href="{{ route('admin.teacher-availability.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.teacher-availability.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Teacher Availability
            </a>
            @endif

            @if(routeExists('admin.leaves.index'))
            <a href="{{ route('admin.leaves.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.leaves.index') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Leave Requests
            </a>
            @endif

            @if(routeExists('admin.leaves.my'))
            <a href="{{ route('admin.leaves.my') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.leaves.my') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Leaves
            </a>
            @endif

            {{-- SALARY --}}
            @if(routeExists('admin.salaries.index') || routeExists('admin.salaries.templates'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Salary</div>
            </div>
            @endif

            @if(routeExists('admin.salaries.index'))
            <a href="{{ route('admin.salaries.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.salaries.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Salary Dashboard
            </a>
            @endif

            @if(routeExists('admin.salaries.templates'))
            <a href="{{ route('admin.salaries.templates') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.salaries.templates') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Salary Templates
            </a>
            @endif

            {{-- FEES MANAGEMENT --}}
            @if(routeExists('admin.fee-types.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fees Management</div>
            </div>
            <div x-data="{ openFees: {{ request()->routeIs('admin.fee-types.*','admin.fee-submissions.*','admin.fee-installments.*','admin.invoices.*','admin.banks.*','admin.discounts.*','admin.discount-assignments.*','admin.fee-reports.*') ? 'true' : 'false' }} }">
                <button @click="openFees = !openFees" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fee-types.*','admin.fee-submissions.*','admin.fee-installments.*','admin.invoices.*','admin.banks.*','admin.discounts.*','admin.discount-assignments.*','admin.fee-reports.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Fees Management</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openFees }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openFees" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0" class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                    @if(routeExists('admin.fee-types.index'))<a href="{{ route('admin.fee-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📋 Fee Types</a>@endif
                    @if(routeExists('admin.fee-submissions.index'))<a href="{{ route('admin.fee-submissions.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-submissions.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Fee Submissions</a>@endif
                    @if(routeExists('admin.fee-installments.index'))<a href="{{ route('admin.fee-installments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-installments.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📅 Installments</a>@endif
                    @if(routeExists('admin.invoices.index'))<a href="{{ route('admin.invoices.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.invoices.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📄 Invoices</a>@endif
                    @if(routeExists('admin.banks.index'))<a href="{{ route('admin.banks.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.banks.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🏦 Banks</a>@endif
                    @if(routeExists('admin.discounts.index'))<a href="{{ route('admin.discounts.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.discounts.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🏷️ Discounts</a>@endif
                    @if(routeExists('admin.discount-assignments.index'))<a href="{{ route('admin.discount-assignments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.discount-assignments.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">👨‍🎓 Student Discounts</a>@endif
                    @if(routeExists('admin.fee-reports.index'))<a href="{{ route('admin.fee-reports.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.fee-reports.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📈 Fee Reports</a>@endif
                </div>
            </div>
            @endif

            {{-- EXAMINATION --}}
            @if(routeExists('admin.exams.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Examination</div>
            </div>
            <div x-data="{ openExam: {{ request()->routeIs('admin.exam-types.*','admin.exam-groups.*','admin.grade-scales.*','admin.exams.*','admin.exam-marks.*','admin.exams.reports.*','admin.results.*','admin.exam-results.*') ? 'true' : 'false' }} }">
                <button @click="openExam = !openExam" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exam-types.*','admin.exam-groups.*','admin.grade-scales.*','admin.exams.*','admin.exam-marks.*','admin.exams.reports.*','admin.results.*','admin.exam-results.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Examination</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openExam }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openExam" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0" class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                    @if(routeExists('admin.exam-types.index'))<a href="{{ route('admin.exam-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📋 Exam Types</a>@endif
                    @if(routeExists('admin.exam-groups.index'))<a href="{{ route('admin.exam-groups.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-groups.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📁 Exam Groups</a>@endif
                    @if(routeExists('admin.grade-scales.index'))<a href="{{ route('admin.grade-scales.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.grade-scales.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📈 Grade Scales</a>@endif
                    @if(routeExists('admin.exams.index'))<a href="{{ route('admin.exams.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📋 Exam Dashboard</a>@endif
                    @if(routeExists('admin.exams.create'))<a href="{{ route('admin.exams.create') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.create') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">➕ Create Exam</a>@endif
                    @if(routeExists('admin.exam-marks.index'))<a href="{{ route('admin.exam-marks.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-marks.index') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Marks Dashboard</a>@endif
                    @if(routeExists('admin.exam-marks.create'))<a href="{{ route('admin.exam-marks.create') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-marks.create') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">✏️ Enter Marks</a>@endif
                    @if(routeExists('admin.exams.reports.index'))<a href="{{ route('admin.exams.reports.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exams.reports.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Reports</a>@endif
                    @if(routeExists('admin.results.class-wise'))<a href="{{ route('admin.results.class-wise') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.results.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📋 Results</a>@endif
                    @if(routeExists('admin.exam-results.index'))<a href="{{ route('admin.exam-results.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.exam-results.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📊 Results Analysis</a>@endif
                </div>
            </div>
            @endif

            {{-- CURRICULUM --}}
            @if(routeExists('admin.subjects.index') || routeExists('admin.subject-assignments.index') || routeExists('admin.class-subject.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Curriculum</div>
            </div>
            @endif

            @if(routeExists('admin.subjects.index'))
            <a href="{{ route('admin.subjects.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Subjects
            </a>
            @endif

            @if(routeExists('admin.subject-assignments.index'))
            <a href="{{ route('admin.subject-assignments.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.subject-assignments.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Subject Assignments
            </a>
            @endif

            @if(routeExists('admin.class-subject.index'))
            <a href="{{ route('admin.class-subject.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.class-subject.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Section-wise Subjects
            </a>
            @endif

            {{-- TIMETABLE --}}
            @if(routeExists('admin.school-timings.index') || routeExists('admin.timetable-reports.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Timetable</div>
            </div>
            @endif

            @if(routeExists('admin.school-timings.index'))
            <a href="{{ route('admin.school-timings.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.school-timings.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                School Timings
            </a>
            @endif

            @if(routeExists('admin.timetable-reports.index'))
            <a href="{{ route('admin.timetable-reports.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.timetable-reports.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Timetable Dashboard
            </a>
            @endif

            @if(routeExists('admin.timetable-reports.class'))<a href="{{ route('admin.timetable-reports.class') }}" class="ml-6 flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.timetable-reports.class') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📚 Class View</a>@endif
            @if(routeExists('admin.timetable-reports.section'))<a href="{{ route('admin.timetable-reports.section') }}" class="ml-6 flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.timetable-reports.section') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📋 Section View</a>@endif
            @if(routeExists('admin.timetable-reports.teacher'))<a href="{{ route('admin.timetable-reports.teacher') }}" class="ml-6 flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.timetable-reports.teacher') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">👨‍🏫 Teacher View</a>@endif

            {{-- INFRASTRUCTURE --}}
            @if(routeExists('admin.blocks.index') || routeExists('admin.floors.index') || routeExists('admin.rooms.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Infrastructure</div>
            </div>
            @endif

            @if(routeExists('admin.blocks.index'))<a href="{{ route('admin.blocks.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.blocks.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Blocks</a>@endif
            @if(routeExists('admin.floors.index'))<a href="{{ route('admin.floors.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.floors.*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Floors</a>@endif
            @if(routeExists('admin.rooms.index'))<a href="{{ route('admin.rooms.index') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.rooms.index') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Rooms</a>@endif
            @if(routeExists('admin.rooms.assignments'))<a href="{{ route('admin.rooms.assignments') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.rooms.assignments') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"><svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>Room Assignments</a>@endif

            {{-- TRANSPORT --}}
            @if(routeExists('admin.vehicles.index') || routeExists('admin.drivers.index') || routeExists('admin.transport-routes.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Transport</div>
            </div>
            <div x-data="{ openTransport: {{ request()->routeIs('admin.vehicles.*','admin.drivers.*','admin.transport-routes.*','admin.student-transports.*','admin.transport-fee-types.*','admin.transport-fee-payments.*','admin.vehicle-trips.*') ? 'true' : 'false' }} }">
                <button @click="openTransport = !openTransport" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.vehicles.*','admin.drivers.*','admin.transport-routes.*','admin.student-transports.*','admin.transport-fee-types.*','admin.transport-fee-payments.*','admin.vehicle-trips.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2M9 17h6M5 11h13"/></svg>
                        <span>Transport</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openTransport }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openTransport" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0" class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                    @if(routeExists('admin.vehicles.index'))<a href="{{ route('admin.vehicles.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.vehicles.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🚌 Vehicles</a>@endif
                    @if(routeExists('admin.drivers.index'))<a href="{{ route('admin.drivers.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.drivers.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🧑‍✈️ Drivers</a>@endif
                    @if(routeExists('admin.transport-routes.index'))<a href="{{ route('admin.transport-routes.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.transport-routes.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🗺️ Routes</a>@endif
                    @if(routeExists('admin.student-transports.index'))<a href="{{ route('admin.student-transports.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.student-transports.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">👨‍🎓 Student Assignments</a>@endif
                    @if(routeExists('admin.vehicle-trips.index'))<a href="{{ route('admin.vehicle-trips.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.vehicle-trips.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">📍 Trip Logs</a>@endif
                    @if(routeExists('admin.transport-fee-types.index'))<a href="{{ route('admin.transport-fee-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.transport-fee-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">💳 Fee Types</a>@endif
                    @if(routeExists('admin.transport-fee-payments.index'))<a href="{{ route('admin.transport-fee-payments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.transport-fee-payments.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">💰 Fee Payments</a>@endif
                </div>
            </div>
            @endif

            {{-- HOSTEL --}}
            @if(routeExists('admin.hostels.index') || routeExists('admin.hostel-rooms.index'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Hostel</div>
            </div>
            <div x-data="{ openHostel: {{ request()->routeIs('admin.hostels.*','admin.hostel-rooms.*','admin.hostel-room-types.*','admin.hostel-allocations.*','admin.hostel-fee-types.*','admin.hostel-fee-payments.*','admin.hostel-staff.*') ? 'true' : 'false' }} }">
                <button @click="openHostel = !openHostel" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.hostels.*','admin.hostel-rooms.*','admin.hostel-room-types.*','admin.hostel-allocations.*','admin.hostel-fee-types.*','admin.hostel-fee-payments.*','admin.hostel-staff.*') ? 'bg-indigo-600/20 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4 8 4v14M9 21v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
                        <span>Hostel</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openHostel }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openHostel" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 -translate-y-2" x-transition:enter-end="transform opacity-100 translate-y-0" class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                    @if(routeExists('admin.hostels.index'))<a href="{{ route('admin.hostels.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostels.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🏠 Hostels</a>@endif
                    @if(routeExists('admin.hostel-room-types.index'))<a href="{{ route('admin.hostel-room-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostel-room-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🛏️ Room Types</a>@endif
                    @if(routeExists('admin.hostel-rooms.index'))<a href="{{ route('admin.hostel-rooms.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostel-rooms.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🚪 Rooms</a>@endif
                    @if(routeExists('admin.hostel-allocations.index'))<a href="{{ route('admin.hostel-allocations.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostel-allocations.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">👨‍🎓 Allocations</a>@endif
                    @if(routeExists('admin.hostel-staff.index'))<a href="{{ route('admin.hostel-staff.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostel-staff.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">🧑‍💼 Warden / Staff</a>@endif
                    @if(routeExists('admin.hostel-fee-types.index'))<a href="{{ route('admin.hostel-fee-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostel-fee-types.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">💳 Fee Types</a>@endif
                    @if(routeExists('admin.hostel-fee-payments.index'))<a href="{{ route('admin.hostel-fee-payments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm {{ request()->routeIs('admin.hostel-fee-payments.*') ? 'text-white bg-gray-700/40' : 'text-gray-400 hover:text-white hover:bg-gray-700/30' }}">💰 Fee Payments</a>@endif
                </div>
            </div>
            @endif

            {{-- TEACHER REPORTS --}}
            @if(routeExists('admin.exams.reports.teacher-wise'))
            <div class="pt-3 mt-2 border-t border-gray-700/30">
                <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher Reports</div>
            </div>
            <a href="{{ route('admin.exams.reports.teacher-wise') }}" class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exams.reports.teacher-wise*') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Teacher Wise Results
            </a>
            @endif

        @endrole {{-- END ADMIN --}}

        {{-- ============================================================ --}}
        {{-- TEACHER SECTIONS — all teacher.* routes                     --}}
        {{-- ============================================================ --}}
        @role('teacher')
            @php
                $teacherMenu = [
                    'My Teaching' => [
                        ['teacher.school-timings.index', 'teacher.school-timings.*', 'School Timings', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['teacher.subjects.index', 'teacher.subjects.*', 'My Subjects', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['teacher.timetable', 'teacher.timetable', 'My Timetable', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                    'My Students' => [
                        ['teacher.students.index', 'teacher.students.*', 'My Students', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                        ['teacher.reports.students', 'teacher.reports.students', 'Student Performance', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['teacher.report-card.index', 'teacher.report-card.*', 'Report Cards', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                    'Student Attendance' => [
                        ['teacher.attendance.create', 'teacher.attendance.create', 'Mark Attendance', 'M12 4v16m8-8H4'],
                        ['teacher.attendance.index', 'teacher.attendance.index', 'Attendance Dashboard', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['teacher.attendance.report', 'teacher.attendance.report', 'Attendance Report', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                    'My Attendance' => [
                        ['teacher.attendance.check', 'teacher.attendance.check', 'Check In / Check Out', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['teacher.attendance.history', 'teacher.attendance.history', 'My Attendance History', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ],
                    'Exams & Marks' => [
                        ['teacher.exams.index', 'teacher.exams.*', 'My Exams', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['teacher.marks.index', 'teacher.marks.index', 'Marks Overview', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['teacher.marks.create', 'teacher.marks.create', 'Enter Marks', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ['teacher.exam-reports.index', 'teacher.exam-reports.*', 'Exam Reports', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ],
                    'Timetable Reports' => [
                        ['teacher.timetable-reports.index', 'teacher.timetable-reports.index', 'Timetable Dashboard', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['teacher.timetable-reports.class', 'teacher.timetable-reports.class', 'Class View', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['teacher.timetable-reports.section', 'teacher.timetable-reports.section', 'Section View', 'M4 6h16M4 12h16M4 18h16'],
                        ['teacher.timetable-reports.teacher', 'teacher.timetable-reports.teacher', 'Teacher View', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ],
                    'Reports' => [
                        ['teacher.reports.index', 'teacher.reports.index', 'My Reports', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                    'Salary' => [
                        ['teacher.salary.index', 'teacher.salary.*', 'My Salary', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ],
                    'Leaves' => [
                        ['teacher.leaves.index', 'teacher.leaves.index', 'My Leaves', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['teacher.leaves.create', 'teacher.leaves.create', 'Request Leave', 'M12 4v16m8-8H4'],
                    ],
                    'Personal' => [
                        ['teacher.profile', 'teacher.profile*', 'My Profile', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ],
                ];
            @endphp

            @foreach($teacherMenu as $section => $items)
                @php $visible = collect($items)->filter(fn($i) => routeExists($i[0])); @endphp
                @if($visible->isNotEmpty())
                    <div class="pt-3 mt-2 border-t border-gray-700/30">
                        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $section }}</div>
                    </div>
                    @foreach($visible as [$route, $pattern, $label, $icon])
                        <a href="{{ route($route) }}"
                           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs($pattern) ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                            {{ $label }}
                        </a>
                    @endforeach
                @endif
            @endforeach
        @endrole {{-- END TEACHER --}}

        {{-- ============================================================ --}}
        {{-- STUDENT SECTIONS — new modular student menu                 --}}
        {{-- ============================================================ --}}
        @role('student')
            @php
                $studentMenu = [
                    'My Account' => [
                        ['student.profile', 'student.profile', 'My Profile', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ],
                    'Academics' => [
                        ['student.attendance.index', 'student.attendance.*', 'My Attendance', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['student.timetable.index', 'student.timetable.*', 'My Timetable', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['student.subjects.index', 'student.subjects.*', 'My Subjects', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ],
                    'Exams & Results' => [
                        ['student.exams.index', 'student.exams.*', 'Exam Schedule', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['student.results.index', 'student.results.*', 'My Results', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                    'Finance' => [
                        ['student.fees.index', 'student.fees.*', 'Fees & Invoices', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ],
                    'Facilities' => [
                        ['student.transport.index', 'student.transport.*', 'Transport', 'M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM5 17H3v-6l2-5h9l4 5h1a2 2 0 012 2v4h-2M9 17h6M5 11h13'],
                        ['student.hostel.index', 'student.hostel.*', 'Hostel', 'M3 21h18M5 21V7l8-4 8 4v14M9 21v-6a2 2 0 012-2h2a2 2 0 012 2v6'],
                    ],
                    'Documents' => [
                        ['student.certificates.index', 'student.certificates.*', 'Certificates', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                ];
            @endphp

            @foreach($studentMenu as $section => $items)
                @php $visible = collect($items)->filter(fn($i) => routeExists($i[0])); @endphp
                @if($visible->isNotEmpty())
                    <div class="pt-3 mt-2 border-t border-gray-700/30">
                        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $section }}</div>
                    </div>
                    @foreach($visible as [$route, $pattern, $label, $icon])
                        <a href="{{ route($route) }}"
                           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs($pattern) ? 'bg-indigo-600 shadow-lg shadow-indigo-500/25 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                            {{ $label }}
                        </a>
                    @endforeach
                @endif
            @endforeach
        @endrole {{-- END STUDENT --}}

    </nav>
</aside>

<script>
    document.addEventListener('alpine:init', function() {
        console.log('Alpine.js initialized successfully');
    });
</script>