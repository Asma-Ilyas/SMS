@extends('layouts.app')
@section('title', 'Attendance Report')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Attendance Report</h1>

    {{-- Filter Form --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('admin.studentattendance.report') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <select name="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        @php
                            $gradeName = optional($class->grade)->name ?? 'Class';
                            $streamName = optional($class->stream)->name ?? '';
                            $section = $class->section ?? '';
                            $displayName = trim($gradeName . ' ' . $streamName . ' ' . $section);
                            if (empty($displayName)) $displayName = 'Class #' . $class->id;
                        @endphp
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $displayName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                <select name="student_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300">
                    <option value="">All</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                </select>
            </div>
            <div class="md:col-span-2 lg:col-span-5 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">🔍 Filter</button>
                <a href="{{ route('admin.studentattendance.report') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    {{-- Summary Table (if class and date range selected) --}}
    @if($summary)
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-semibold text-gray-800">📈 Attendance Summary ({{ request('from_date') }} to {{ request('to_date') }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Present</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Absent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Late</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Half Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($summary as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.studentattendance.student', $stat['student']->id) }}" class="text-blue-600 hover:underline">
                                {{ $stat['student']->first_name }} {{ $stat['student']->last_name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $stat['total_days'] }}</td>
                        <td class="px-6 py-4 text-green-600 font-medium">{{ $stat['present'] }}</td>
                        <td class="px-6 py-4 text-red-600">{{ $stat['absent'] }}</td>
                        <td class="px-6 py-4 text-yellow-600">{{ $stat['late'] }}</td>
                        <td class="px-6 py-4 text-orange-600">{{ $stat['half_day'] }}</td>
                        <td class="px-6 py-4 font-bold">
                            <span class="px-2 py-1 rounded-full text-xs 
                                {{ $stat['percentage'] >= 75 ? 'bg-green-100 text-green-800' : ($stat['percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $stat['percentage'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Detailed Records Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-semibold text-gray-800">📋 Attendance Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marked By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $att->student->first_name }} {{ $att->student->last_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $class = $att->class;
                                if ($class) {
                                    $gName = optional($class->grade)->name ?? 'Class';
                                    $sName = optional($class->stream)->name ?? '';
                                    $sec = $class->section ?? '';
                                    $classDisplay = trim($gName . ' ' . $sName . ' ' . $sec);
                                    echo $classDisplay ?: 'Class #' . $class->id;
                                } else {
                                    echo 'N/A';
                                }
                            @endphp
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $att->subject->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($att->status)
                                @case('present') ✅ Present @break
                                @case('absent') ❌ Absent @break
                                @case('late') ⏰ Late @break
                                @case('half_day') 🌓 Half Day @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $att->staff->name ?? 'Admin' }}</td>
                        <td class="px-6 py-4">{{ $att->remarks ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-8">No attendance records found for the selected filters.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $attendances->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection