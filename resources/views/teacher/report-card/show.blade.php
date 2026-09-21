@extends('layouts.app')
@section('title', 'Report Card')
@section('content')
@php $pass = $percentage >= $exam->passing_percentage; @endphp
<div class="space-y-6 max-w-4xl">
    <div class="print:hidden">@include('teacher.partials.header', ['title' => 'Report Card', 'subtitle' => $exam->name])</div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="text-center border-b pb-4 mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $exam->name }}</h2>
            <p class="text-sm text-gray-500">{{ $exam->type_name }} · {{ $student->session_name }}</p>
        </div>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6">
            <div><dt class="text-xs uppercase text-gray-500">Student</dt><dd class="font-medium">{{ $student->first_name }} {{ $student->last_name }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Admission #</dt><dd>{{ $student->admission_number }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Class</dt><dd>{{ $student->grade_name }}-{{ $student->section_name }} @if($student->stream_name)({{ $student->stream_name }})@endif</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Father</dt><dd>{{ $student->father_name }}</dd></div>
        </dl>

        <table class="min-w-full text-sm border">
            <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2 border">Subject</th><th class="px-4 py-2 border">Obtained</th><th class="px-4 py-2 border">Max</th><th class="px-4 py-2 border">Passing</th><th class="px-4 py-2 border">%</th><th class="px-4 py-2 border">Result</th></tr></thead>
            <tbody>
                @forelse($marks as $m)
                    <tr>
                        <td class="px-4 py-2 border font-medium">{{ $m->subject }}</td>
                        <td class="px-4 py-2 border">{{ $m->obtained ?? 'Absent' }}</td>
                        <td class="px-4 py-2 border">{{ $m->max }}</td>
                        <td class="px-4 py-2 border">{{ $m->passing }}</td>
                        <td class="px-4 py-2 border">{{ $m->pct !== null ? $m->pct.'%' : '—' }}</td>
                        <td class="px-4 py-2 border">{{ $m->passed === null ? '—' : ($m->passed ? 'Pass' : 'Fail') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 border">Subject marks not available.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr><td class="px-4 py-2 border">Total</td><td class="px-4 py-2 border">{{ $totalObtained }}</td><td class="px-4 py-2 border">{{ $totalMax }}</td><td class="px-4 py-2 border"></td><td class="px-4 py-2 border">{{ number_format($percentage,1) }}%</td><td class="px-4 py-2 border">{{ $pass ? 'Pass' : 'Fail' }}</td></tr>
            </tfoot>
        </table>

        <div class="grid grid-cols-3 gap-4 mt-6 text-center text-sm">
            <div class="bg-gray-50 rounded-lg p-3"><div class="text-xs uppercase text-gray-500">Grade</div><div class="text-xl font-bold">{{ $grade }}</div></div>
            <div class="bg-gray-50 rounded-lg p-3"><div class="text-xs uppercase text-gray-500">Rank</div><div class="text-xl font-bold">{{ $result->rank_in_class ?? '—' }}@if($result && $result->rank_in_class) <span class="text-sm text-gray-400">/ {{ $strength }}</span>@endif</div></div>
            <div class="bg-gray-50 rounded-lg p-3"><div class="text-xs uppercase text-gray-500">Result</div><div class="text-xl font-bold {{ $pass ? 'text-green-600' : 'text-red-600' }}">{{ $pass ? 'PASS' : 'FAIL' }}</div></div>
        </div>
        @if($result && $result->remarks)<p class="mt-4 text-sm text-gray-600"><strong>Remarks:</strong> {{ $result->remarks }}</p>@endif
    </div>

    <div class="print:hidden flex gap-4">
        <a href="{{ route('teacher.report-card.index', ['section_id' => $student->class_section_id, 'exam_id' => $exam->id]) }}" class="text-sm text-indigo-600 hover:underline">← Back</a>
        <button onclick="window.print()" class="text-sm text-gray-600 hover:underline">🖨 Print</button>
    </div>
</div>
@endsection
