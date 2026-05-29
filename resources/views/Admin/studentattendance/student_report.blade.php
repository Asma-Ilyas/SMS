@extends('layouts.app')
@section('title', 'Attendance Report for '.$student->first_name)
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👤 Attendance: {{ $student->first_name }} {{ $student->last_name }}</h1>
        <a href="{{ route('admin.studentattendance.report') }}" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">← Back to Report</a>
    </div>

    {{-- Monthly Summary Cards --}}
    @if($monthlyStats->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($monthlyStats as $stat)
            @php
                $total = max($stat->total, 1);
                $percentage = round(($stat->present + ($stat->late * 0.5) + ($stat->half_day * 0.5)) / $total * 100);
                $colorClass = $percentage >= 75 ? 'border-green-500' : ($percentage >= 50 ? 'border-yellow-500' : 'border-red-500');
            @endphp
            <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $colorClass }}">
                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $stat->month)->format('F Y') }}</p>
                <p class="text-2xl font-bold">{{ $percentage }}%</p>
                <p class="text-xs text-gray-600">Present: {{ $stat->present }} | Absent: {{ $stat->absent }}</p>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Detailed Records --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-semibold">📅 All Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Class</th>
                        <th class="px-6 py-3 text-left">Subject</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Marked By</th>
                        <th class="px-6 py-3 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $class = $att->class;
                                if ($class) {
                                    $gradeName = optional($class->grade)->name ?? 'Class';
                                    $streamName = optional($class->stream)->name ?? '';
                                    $section = $class->section ?? '';
                                    $display = trim($gradeName . ' ' . $streamName . ' ' . $section);
                                    echo $display ?: 'Class #' . $class->id;
                                } else {
                                    echo 'N/A';
                                }
                            @endphp
                        </td>
                        <td class="px-6 py-4">{{ $att->subject->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @switch($att->status)
                                @case('present') ✅ Present @break
                                @case('absent') ❌ Absent @break
                                @case('late') ⏰ Late @break
                                @case('half_day') 🌓 Half Day @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4">{{ $att->staff->name ?? 'Admin' }}</td>
                        <td class="px-6 py-4">{{ $att->remarks ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-8">No attendance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $attendances->links() }}</div>
    </div>
</div>
@endsection