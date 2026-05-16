<aside class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex-shrink-0 h-screen sticky top-0 overflow-y-auto shadow-xl">
    <!-- Logo -->
    <div class="p-5 border-b border-gray-700/50 flex items-center space-x-3">
        <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center shadow-md">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold tracking-tight bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">SchoolAdmin</h2>
    </div>

    <nav class="mt-6 px-3 space-y-1.5 pb-24">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <!-- ========== ACADEMICS ========== -->
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Academics</div>
        </div>

        <a href="{{ route('admin.sessions.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.sessions.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Academic Sessions
        </a>

        <a href="{{ route('admin.classes.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.classes.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Classes
        </a>

        <a href="{{ route('admin.grades.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.grades.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Grades
        </a>

        <a href="{{ route('admin.streams.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.streams.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Streams
        </a>

        <a href="{{ route('admin.students.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Students
        </a>

        <a href="{{ route('admin.transfers.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.transfers.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Student Transfers
        </a>

        <a href="{{ route('admin.transfer-certificates.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.transfer-certificates.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Transfer Certificates
        </a>

        <a href="{{ route('admin.certificates.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.certificates.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Certificates
        </a>

        <!-- ========== FEES MANAGEMENT ========== -->
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fees</div>
        </div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 text-gray-300 hover:bg-gray-700/50 hover:text-white">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Fees Management</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                <a href="{{ route('admin.fee-types.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Fee Types</a>
                <a href="{{ route('admin.fee-submissions.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Fee Submissions</a>
                <a href="{{ route('admin.fee-installments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Installments</a>
                <a href="{{ route('admin.invoices.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Invoices</a>
                <a href="{{ route('admin.banks.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Banks</a>
                <a href="{{ route('admin.discounts.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Discounts</a>
                <a href="{{ route('admin.discount-assignments.index') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Student Discounts</a>
            </div>
        </div>

        <a href="{{ route('admin.fee-reports.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.fee-reports.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Fee Reports
        </a>

        <!-- ========== STAFF & HR ========== -->
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Staff & HR</div>
        </div>

        <a href="{{ route('admin.staff.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.staff.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Staff / Teachers
        </a>

        <a href="{{ route('admin.employee-categories.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.employee-categories.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
            </svg>
            Staff Categories
        </a>

        <a href="{{ route('admin.attendance.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.attendance.*') && !request()->routeIs('admin.attendance.check') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Attendance Records
        </a>

        <a href="{{ route('admin.salaries.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.salaries.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Salary Management
        </a>

        <a href="{{ route('admin.leaves.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.leaves.index') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Leave Requests
        </a>

        <a href="{{ route('admin.leaves.my') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.leaves.my') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            My Leaves
        </a>

        <a href="{{ route('admin.attendance.check') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.attendance.check') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Mark Attendance
        </a>

        <!-- ========== EXAMINATION ========== -->
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Examination</div>
        </div>

        <a href="{{ route('admin.exams.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exams.*') && !request()->routeIs('admin.exam-results.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Manage Exams
        </a>

        <a href="{{ route('admin.exam-groups.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exam-groups.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Manage Groups
        </a>

        <a href="{{ route('admin.exam-types.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.exam-types.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Exam Types
        </a>

        <!-- Exam Results dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 text-gray-300 hover:bg-gray-700/50 hover:text-white">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Exam Results</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 -translate-y-2"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 class="ml-7 mt-1 space-y-1 border-l border-gray-700/50">
                <a href="{{ route('admin.exam-results.bulk-print-form', 'placeholder') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30" 
                   onclick="alert('Please select an exam from the Manage Exams page first.'); return false;">Bulk Print Results</a>
                <a href="{{ route('admin.exam-results.academic-report', ['student' => 'placeholder']) }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30"
                   onclick="alert('Please go to Academic Report from the exam results section.'); return false;">Academic Report</a>
                <a href="{{ route('admin.exam-results.multi-group-report.form') }}" class="flex items-center px-4 py-2 rounded-lg text-sm text-gray-400 hover:text-white hover:bg-gray-700/30">Multi‑Group Report</a>
            </div>
        </div>

        <!-- ========== CURRICULUM ========== -->
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Curriculum</div>
        </div>

        <a href="{{ route('admin.subjects.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Subjects
        </a>

        <a href="{{ route('admin.subject-assignments.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.subject-assignments.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Subject Assignments
        </a>

        <!-- NEW: Section-wise Subjects -->
        <a href="{{ route('admin.class-subject.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.class-subject.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Section-wise Subjects
        </a>

        <!-- ========== ADDITIONAL MODULES ========== -->
        <div class="pt-3 mt-2 border-t border-gray-700/30">
            <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Other</div>
        </div>

        <a href="{{ route('admin.studentattendance.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.studentattendance.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Student Attendance
        </a>

        <a href="{{ route('admin.timetable.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.timetable.*') ? 'bg-indigo-600 shadow-md text-white' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21h5a2 2 0 002-2V7a2 2 0 00-2-2h-5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Time Table
        </a>
    </nav>

    <!-- Bottom user profile -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700/50 bg-gray-800/50 backdrop-blur-sm">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold shadow-md">AD</div>
            <div class="flex-1">
                <p class="text-sm font-medium">Admin User</p>
                <p class="text-xs text-gray-400">admin@school.com</p>
            </div>
            <button class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </div>
    </div>
</aside>