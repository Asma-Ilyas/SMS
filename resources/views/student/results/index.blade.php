@extends('layouts.app')
@section('title', 'My Results')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'My Results', 'subtitle' => 'Only published results are shown'])

    @if($results->isEmpty())
        @include('student.partials.empty', ['message' => 'No published results yet.'])
    @else
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">Exams</div><div class="text-3xl font-bold text-gray-800">{{ $results->count() }}</div></div>
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">Average %</div><div class="text-3xl font-bold text-indigo-600">{{ $avg }}%</div></div>
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">Best</div><div class="text-3xl font-bold text-green-600">{{ number_format($best->percentage, 1) }}%</div><div class="text-xs text-gray-500">{{ $best->exam_name }}</div></div>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3">Exam</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Marks</th><th class="px-4 py-3">%</th><th class="px-4 py-3">Grade</th><th class="px-4 py-3">Rank</th><th class="px-4 py-3">Result</th><th></th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($results as $r)
                        @php $pass = $r->percentage >= $r->passing_percentage; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $r->exam_name }}<div class="text-xs text-gray-400">{{ $r->type_name }}</div></td>
                            <td class="px-4 py-3">{{ $fmt($r->end_date) }}</td>
                            <td class="px-4 py-3">{{ rtrim(rtrim(number_format($r->total_marks,2),'0'),'.') }} / {{ rtrim(rtrim(number_format($r->total_max_marks,2),'0'),'.') }}</td>
                            <td class="px-4 py-3 font-semibold">{{ number_format($r->percentage,1) }}%</td>
                            <td class="px-4 py-3">{{ $r->grade ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $r->rank_in_class ?? '—' }}</td>
                            <td class="px-4 py-3">@include('student.partials.badge', ['status' => $pass ? 'pass' : 'fail'])</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('student.results.show', $r->exam_id) }}" class="text-indigo-600 hover:underline">Details</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
