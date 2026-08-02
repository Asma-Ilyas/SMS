@extends('layouts.app')

@section('title', 'Teacher Wise Exam Results')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container-fluid px-4 py-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-indigo-600"></i>
                    Teacher-Wise Results
                </h1>
                <p class="text-sm text-gray-500 mt-1">Select a teacher and subject to view detailed results</p>
            </div>
            @if(request('teacher_id') && request('subject_id') && isset($subjectResults) && count($subjectResults) > 0)
                <a href="{{ route('admin.exams.reports.teacher-wise.export', ['teacher_id' => request('teacher_id'), 'subject_id' => request('subject_id'), 'exam_id' => request('exam_id')]) }}" 
                   class="mt-3 md:mt-0 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Export CSV
                </a>
            @endif
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
            <form method="GET" action="{{ route('admin.exams.reports.teacher-wise') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Teacher Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-tie text-indigo-500 mr-1"></i>
                            Select Teacher <span class="text-red-500">*</span>
                        </label>
                        <select name="teacher_id" id="teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-700">
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" 
                                    {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    ({{ $teacher->employee_id ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Subject Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-book text-indigo-500 mr-1"></i>
                            Select Subject <span class="text-red-500">*</span>
                        </label>
                        <select name="subject_id" id="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-700">
                            <option value="">-- Select Subject --</option>
                            @if(isset($availableSubjects) && $availableSubjects->count() > 0)
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} 
                                        @if($subject->code)
                                            ({{ $subject->code }})
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <!-- Exam Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>
                            Select Exam (Optional)
                        </label>
                        <select name="exam_id" id="exam_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-700">
                            <option value="">All Exams</option>
                            @if(isset($availableExams) && $availableExams->count() > 0)
                                @foreach($availableExams as $exam)
                                    <option value="{{ $exam->id }}" 
                                        {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} 
                                        @if($exam->classSection)
                                            - {{ $exam->classSection->section_name }}
                                        @endif
                                        ({{ \Carbon\Carbon::parse($exam->start_date)->format('d M Y') }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-search mr-2"></i>View Results
                        </button>
                        <a href="{{ route('admin.exams.reports.teacher-wise') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition duration-200">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Selected Teacher Info -->
            @if(request('teacher_id') && $selectedTeacher)
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-semibold text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i> Selected Teacher:
                </p>
                <p class="text-xs text-blue-700 mt-1">
                    <strong>{{ $selectedTeacher->first_name }} {{ $selectedTeacher->last_name }}</strong> 
                    ({{ $selectedTeacher->employee_id ?? 'N/A' }})
                </p>
                @if(request('subject_id') && $selectedSubject)
                <p class="text-xs text-blue-700 mt-1">
                    Subject: <strong class="text-indigo-600">{{ $selectedSubject->name }}</strong>
                    @if($selectedSubject->code)
                        ({{ $selectedSubject->code }})
                    @endif
                </p>
                @endif
                @if(isset($subjectResults) && count($subjectResults) > 0)
                <p class="text-xs text-green-700 mt-1">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    Found <strong>{{ count($subjectResults) }}</strong> student results
                </p>
                @endif
            </div>
            @endif
        </div>

        <!-- Results Content -->
        @if(request('teacher_id') && request('subject_id'))
            @if(isset($subjectResults) && count($subjectResults) > 0)
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Students</p>
                                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $subjectTotals['total_students'] ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-indigo-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Passed</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $subjectTotals['total_passed'] ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Failed</p>
                                <p class="text-2xl font-bold text-red-600 mt-1">{{ $subjectTotals['total_failed'] ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Pass %</p>
                                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($subjectTotals['pass_percentage'] ?? 0, 1) }}%</p>
                            </div>
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-percentage text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher & Subject Info -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-sm border border-indigo-100 p-4 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-user-tie text-indigo-600 mr-2"></i>
                                {{ $selectedTeacher->first_name }} {{ $selectedTeacher->last_name }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-book text-indigo-500 mr-1"></i>
                                Subject: <strong>{{ $selectedSubject->name }}</strong>
                                @if($selectedSubject->code)
                                    ({{ $selectedSubject->code }})
                                @endif
                            </p>
                        </div>
                        <div class="mt-2 md:mt-0 flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-file-alt mr-1"></i>
                                {{ count($subjectResults) }} Records
                            </span>
                            @if($subjectTotals['average_marks'] ?? 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Avg: {{ number_format($subjectTotals['average_marks'], 1) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Grade Distribution -->
                @if(!empty($subjectTotals['grade_distribution']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                        Grade Distribution - {{ $selectedSubject->name }}
                    </h3>
                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        @php
                            $gradeColors = [
                                'A+' => 'emerald',
                                'A' => 'green',
                                'B+' => 'blue',
                                'B' => 'indigo',
                                'C' => 'yellow',
                                'D' => 'orange',
                                'F' => 'red'
                            ];
                            $gradeIcons = [
                                'A+' => 'fa-star',
                                'A' => 'fa-check-circle',
                                'B+' => 'fa-thumbs-up',
                                'B' => 'fa-check',
                                'C' => 'fa-minus',
                                'D' => 'fa-exclamation',
                                'F' => 'fa-times'
                            ];
                        @endphp
                        @foreach($subjectTotals['grade_distribution'] as $grade => $count)
                            @php
                                $percentage = ($count / $subjectTotals['total_students']) * 100;
                                $color = $gradeColors[$grade] ?? 'gray';
                                $icon = $gradeIcons[$grade] ?? 'fa-circle';
                            @endphp
                            <div class="bg-gray-50 rounded-lg p-3 text-center hover:shadow-md transition duration-200">
                                <div class="text-2xl font-bold text-{{ $color }}-600">
                                    <i class="fas {{ $icon }} mr-1"></i>
                                    {{ $grade }}
                                </div>
                                <div class="text-lg font-bold text-gray-800">{{ $count }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($percentage, 1) }}%</div>
                                <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-{{ $color }}-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Passed & Failed Students Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Passed Students -->
                    <div class="bg-green-50 rounded-xl shadow-sm border border-green-200 p-4">
                        <h4 class="text-sm font-semibold text-green-800 flex items-center mb-3">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            Passed Students ({{ $subjectTotals['total_passed'] ?? 0 }})
                        </h4>
                        @if(isset($passedStudents) && count($passedStudents) > 0)
                            <div class="max-h-48 overflow-y-auto">
                                <ul class="space-y-1">
                                    @foreach($passedStudents as $student)
                                        <li class="text-sm text-green-700 flex items-center justify-between px-2 py-1 bg-green-100/50 rounded">
                                            <span>
                                                <i class="fas fa-user-graduate text-green-600 mr-1"></i>
                                                {{ $student['student_name'] }}
                                            </span>
                                            <span class="text-xs font-medium">
                                                {{ number_format($student['percentage'], 1) }}%
                                                ({{ $student['grade'] }})
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-green-600">No students passed</p>
                        @endif
                    </div>

                    <!-- Failed Students -->
                    <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-4">
                        <h4 class="text-sm font-semibold text-red-800 flex items-center mb-3">
                            <i class="fas fa-times-circle text-red-600 mr-2"></i>
                            Failed Students ({{ $subjectTotals['total_failed'] ?? 0 }})
                        </h4>
                        @if(isset($failedStudents) && count($failedStudents) > 0)
                            <div class="max-h-48 overflow-y-auto">
                                <ul class="space-y-1">
                                    @foreach($failedStudents as $student)
                                        <li class="text-sm text-red-700 flex items-center justify-between px-2 py-1 bg-red-100/50 rounded">
                                            <span>
                                                <i class="fas fa-user-graduate text-red-600 mr-1"></i>
                                                {{ $student['student_name'] }}
                                            </span>
                                            <span class="text-xs font-medium">
                                                {{ number_format($student['percentage'], 1) }}%
                                                ({{ $student['grade'] }})
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-red-600">No students failed</p>
                        @endif
                    </div>
                </div>

                <!-- Detailed Results Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-table text-indigo-500 mr-2"></i>
                            Student Results - {{ $selectedSubject->name }}
                            <span class="ml-2 text-xs text-gray-500 font-normal">
                                ({{ $selectedTeacher->first_name }} {{ $selectedTeacher->last_name }})
                            </span>
                        </h3>
                        <span class="mt-1 md:mt-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-user-graduate mr-1"></i>
                            {{ $subjectTotals['total_students'] ?? 0 }} Students
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="resultsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Date</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Max Marks</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $counter = 1; @endphp
                                @foreach($subjectResults as $result)
                                    <tr class="hover:bg-gray-50 transition duration-150 {{ $result['is_passed'] ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $counter++ }}</td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $result['student_name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $result['student_email'] ?? 'No email' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $result['roll_number'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $result['admission_number'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $result['section_name'] ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ strlen($result['exam_name']) > 20 ? substr($result['exam_name'], 0, 20) . '...' : $result['exam_name'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $result['exam_date'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-center text-sm font-bold text-gray-900">{{ $result['marks_obtained'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $result['max_marks'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $percentage = $result['percentage'] ?? 0;
                                                $color = $percentage >= 80 ? 'green' : ($percentage >= 60 ? 'yellow' : 'red');
                                            @endphp
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-{{ $color }}-100 text-{{ $color }}-700">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $grade = $result['grade'] ?? 'N/A';
                                                $gradeColor = $grade === 'F' ? 'red' : 'indigo';
                                            @endphp
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-{{ $gradeColor }}-100 text-{{ $gradeColor }}-700">
                                                {{ $grade }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($result['is_passed'] ?? false)
                                                <span class="inline-flex items-center text-green-600 font-medium">
                                                    <i class="fas fa-check-circle mr-1"></i>Passed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-red-600 font-medium">
                                                    <i class="fas fa-times-circle mr-1"></i>Failed
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">
                                        <i class="fas fa-calculator text-indigo-500 mr-1"></i>
                                        SUMMARY:
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                        {{ number_format($subjectTotals['average_marks'] ?? 0, 1) }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                        {{ number_format($subjectTotals['average_max_marks'] ?? 0, 1) }}
                                    </td>
                                    <td class="px-4 py-3 text-center" colspan="3">
                                        @php
                                            $avgColor = ($subjectTotals['pass_percentage'] ?? 0) >= 80 ? 'green' : 
                                                       (($subjectTotals['pass_percentage'] ?? 0) >= 60 ? 'yellow' : 'red');
                                        @endphp
                                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-bold bg-{{ $avgColor }}-100 text-{{ $avgColor }}-700">
                                            <i class="fas fa-trophy mr-1"></i>
                                            {{ number_format($subjectTotals['pass_percentage'] ?? 0, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            @else
                <!-- No Results -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chalkboard-teacher text-2xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">No Results Found</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto">No exam marks have been recorded for this teacher and subject combination yet.</p>
                    <a href="{{ route('admin.exams.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Go to Exams
                    </a>
                </div>
            @endif

        @else
            <!-- Select Teacher & Subject -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-tie text-2xl text-indigo-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Select Teacher & Subject</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">Choose a teacher and their corresponding subject from the dropdowns above and click "View Results" to see detailed performance analysis.</p>
                <div class="mt-4 flex items-center justify-center gap-4">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700">
                        <i class="fas fa-users mr-1"></i>
                        {{ $teachers->count() }} Teachers
                    </span>
                    @if(isset($availableSubjects) && $availableSubjects->count() > 0)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-700">
                            <i class="fas fa-book mr-1"></i>
                            {{ $availableSubjects->count() }} Subjects Available
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    if ($('#resultsTable').length) {
        var table = $('#resultsTable').DataTable({
            "pageLength": 25,
            "order": [[1, "asc"]],
            "language": {
                "search": "🔍 Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "zeroRecords": "No matching records found"
            },
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-5"i><"col-sm-7"p>>'
        });
    }

    // Load subjects when teacher changes
    $('#teacher_id').on('change', function() {
        var teacherId = $(this).val();
        var subjectSelect = $('#subject_id');
        var examSelect = $('#exam_id');
        
        // Clear subject dropdown
        subjectSelect.empty();
        subjectSelect.append('<option value="">Loading subjects...</option>');
        
        // Clear exam dropdown
        examSelect.empty();
        examSelect.append('<option value="">All Exams</option>');
        
        if (teacherId) {
            // Load subjects via AJAX
            $.ajax({
                url: "{{ route('admin.exams.reports.teacher-subjects') }}",
                type: 'GET',
                data: { teacher_id: teacherId },
                dataType: 'json',
                success: function(response) {
                    subjectSelect.empty();
                    subjectSelect.append('<option value="">-- Select Subject --</option>');
                    
                    if (response.success && response.subjects && response.subjects.length > 0) {
                        $.each(response.subjects, function(index, subject) {
                            subjectSelect.append('<option value="' + subject.id + '">' + 
                                subject.name + (subject.code ? ' (' + subject.code + ')' : '') + 
                                '</option>');
                        });
                    } else {
                        subjectSelect.append('<option value="">No subjects assigned</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading subjects:', error);
                    subjectSelect.empty();
                    subjectSelect.append('<option value="">Error loading subjects</option>');
                }
            });
        } else {
            subjectSelect.empty();
            subjectSelect.append('<option value="">-- Select Subject --</option>');
        }
    });

    // Submit form when subject changes
    $('#subject_id').on('change', function() {
        var teacherId = $('#teacher_id').val();
        var subjectId = $(this).val();
        
        if (teacherId && subjectId) {
            $('#filterForm').submit();
        }
    });

    // Submit form when exam changes
    $('#exam_id').on('change', function() {
        var teacherId = $('#teacher_id').val();
        var subjectId = $('#subject_id').val();
        
        if (teacherId && subjectId) {
            $('#filterForm').submit();
        }
    });

    // If teacher is pre-selected on page load, load subjects
    @if(request('teacher_id'))
        var teacherId = '{{ request('teacher_id') }}';
        if (teacherId) {
            setTimeout(function() {
                $('#teacher_id').val(teacherId).trigger('change');
            }, 500);
        }
    @endif
});
</script>
@endpush