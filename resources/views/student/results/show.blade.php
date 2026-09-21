@extends('layouts.app')
@section('title', $exam->name.' – Result')
@section('content')
@php $pass = $percentage >= $exam->passing_percentage; @endphp
<div class="space-y-6 max-w-4xl">
    @include('student.partials.header', ['title' => $exam->name.' – Result', 'subtitle' => $s->first_name.' '.$s->last_name.' · Admission # '.$s->admission_number])

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border shadow-sm p-4 text-center"><div class="text-xs uppercase text-gray-500">Total</div><div class="text-2xl font-bold">{{ $totalObtained }} / {{ $totalMax }}</div></div>
        <div class="bg-white rounded-xl border shadow-sm p-4 text-center"><div class="text-xs uppercase text-gray-500">Percentage</div><div class="text-2xl font-bold text-indigo-600">{{ number_format($percentage,1) }}%</div></div>
        <div class="bg-white rounded-xl border shadow-sm p-4 text-center"><div class="text-xs uppercase text-gray-500">Grade</div><div class="text-2xl font-bold">{{ $result->grade ?? '—' }}</div></div>
        <div class="bg-white rounded-xl border shadow-sm p-4 text-center"><div class="text-xs uppercase text-gray-500">Rank</div><div class="text-2xl font-bold">{{ $result->rank_in_class ?? '—' }}@if($result && $result->rank_in_class && $classStrength)<span class="text-sm text-gray-400"> / {{ $classStrength }}</span>@endif</div></div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <span class="font-semibold text-gray-800">Subject-wise marks</span>
            @include('student.partials.badge', ['status' => $pass ? 'pass' : 'fail'])
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left">
                <tr><th class="px-4 py-3">Subject</th><th class="px-4 py-3">Obtained</th><th class="px-4 py-3">Max</th><th class="px-4 py-3">Passing</th><th class="px-4 py-3">%</th><th class="px-4 py-3">Result</th><th class="px-4 py-3">Remarks</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse($marks as $m)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $m->subject }} <span class="text-xs text-gray-400">{{ $m->code }}</span></td>
                        <td class="px-4 py-3">{{ $m->obtained ?? 'Absent / N/A' }}</td>
                        <td class="px-4 py-3">{{ $m->max }}</td>
                        <td class="px-4 py-3">{{ $m->passing }}</td>
                        <td class="px-4 py-3">{{ $m->pct !== null ? $m->pct.'%' : '—' }}</td>
                        <td class="px-4 py-3">@if($m->passed !== null) @include('student.partials.badge', ['status' => $m->passed ? 'pass' : 'fail']) @else — @endif</td>
                        <td class="px-4 py-3 text-gray-500">{{ $m->remarks }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Subject-wise marks are not available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($result && $result->remarks)
        <div class="bg-white rounded-xl border shadow-sm p-5 text-sm text-gray-700"><strong>Remarks:</strong> {{ $result->remarks }}</div>
    @endif

    <div class="flex gap-4 print:hidden">
        <a href="{{ route('student.results.index') }}" class="text-sm text-indigo-600 hover:underline">← All results</a>
        <button onclick="window.print()" class="text-sm text-gray-600 hover:underline">🖨 Print</button>
    </div>
</div>
@endsection
