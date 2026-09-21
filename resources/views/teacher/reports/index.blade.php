@extends('layouts.app')
@section('title', 'My Reports')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Reports', 'subtitle' => 'Workload, attendance activity and class performance'])

    <div class="grid grid-cols-2 xl:grid-cols-5 gap-4">
        @foreach([['Classes', $stats['sections']], ['Subjects', $stats['subjects']], ['Students', $stats['students']], ['Periods / week', $stats['periods']], ['Attendance sessions (30d)', $stats['sessions30']]] as [$l, $v])
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">{{ $l }}</div><div class="text-3xl font-bold text-indigo-600">{{ $v }}</div></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Weekly workload</h3>
            @php $maxP = max(1, max($perDay)); @endphp
            @foreach($days as $d)
                <div class="flex items-center gap-3 text-sm mb-2">
                    <div class="w-24 text-gray-600">{{ ucfirst($d) }}</div>
                    <div class="flex-1 bg-gray-100 rounded-full h-3"><div class="h-3 rounded-full bg-indigo-500" style="width: {{ round($perDay[$d] / $maxP * 100) }}%"></div></div>
                    <div class="w-16 text-right text-gray-600">{{ $perDay[$d] }} periods</div>
                </div>
            @endforeach
            @if($t->max_periods_per_day)<p class="text-xs text-gray-400 mt-3">Limit: {{ $t->max_periods_per_day }}/day · {{ $t->max_periods_per_week }}/week</p>@endif
        </div>

        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">My classes</h3>
            @forelse($pairs as $p)
                <div class="flex justify-between text-sm py-1.5 border-b last:border-0"><span>{{ $p->label }}</span><span class="text-gray-500">{{ $strength[$p->section_id] ?? 0 }} students</span></div>
            @empty <p class="text-sm text-gray-500">No assigned classes.</p> @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Performance in published exams</div>
        @if($performance->isEmpty())
            <div class="p-6 text-sm text-gray-500">No published exam results yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Exam</th><th class="px-4 py-2">Class</th><th class="px-4 py-2">Subject</th><th class="px-4 py-2">Students</th><th class="px-4 py-2">Average</th><th class="px-4 py-2">Pass rate</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($performance as $p)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $p->task->exam->name }}</td>
                            <td class="px-4 py-2">{{ $p->task->pair->section_label }}</td>
                            <td class="px-4 py-2">{{ $p->task->pair->subject_name }}</td>
                            <td class="px-4 py-2">{{ $p->n }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $p->avg !== null ? $p->avg.'%' : '—' }}</td>
                            <td class="px-4 py-2">{{ $p->pass_rate !== null ? $p->pass_rate.'%' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
