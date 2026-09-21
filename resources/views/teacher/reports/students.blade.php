@extends('layouts.app')
@section('title', 'Student Performance')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Student Performance', 'subtitle' => 'Attendance and average marks per student in your subject'])

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[240px]">
            <label class="text-xs text-gray-500">Class · Subject</label>
            <select name="pair" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($pairs as $p)<option value="{{ $p->key }}" @selected($sel && $sel->key === $p->key)>{{ $p->label }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-xs text-gray-500">Attendance from</label><input type="date" name="from" value="{{ $from }}" class="block mt-1 rounded-lg border-gray-300 text-sm"></div>
        <div><label class="text-xs text-gray-500">to</label><input type="date" name="to" value="{{ $to }}" class="block mt-1 rounded-lg border-gray-300 text-sm"></div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
    </form>

    @if($sel)
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">{{ $sel->label }}</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Roll</th><th class="px-4 py-2">Student</th><th class="px-4 py-2">Present</th><th class="px-4 py-2">Absent</th><th class="px-4 py-2">Late</th><th class="px-4 py-2">Attendance</th><th class="px-4 py-2">Exams</th><th class="px-4 py-2">Avg marks</th><th class="px-4 py-2">Grade</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($rows as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-500">{{ $r->student->roll_number ?: '—' }}</td>
                            <td class="px-4 py-2 font-medium"><a class="hover:underline" href="{{ route('teacher.students.show', $r->student->id) }}">{{ $r->student->first_name }} {{ $r->student->last_name }}</a></td>
                            <td class="px-4 py-2">{{ $r->att['present'] }}</td><td class="px-4 py-2">{{ $r->att['absent'] }}</td><td class="px-4 py-2">{{ $r->att['late'] }}</td>
                            <td class="px-4 py-2 font-semibold {{ $r->att['pct'] >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $r->att['total'] ? $r->att['pct'].'%' : '—' }}</td>
                            <td class="px-4 py-2">{{ $r->exams }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $r->avg !== null ? $r->avg.'%' : '—' }}</td>
                            <td class="px-4 py-2">{{ $r->grade }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
