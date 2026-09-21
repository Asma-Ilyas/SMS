@extends('layouts.app')
@section('title', 'Exam Schedule')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'Exam Schedule', 'subtitle' => 'All exams for your class section'])

    @if($exams->isEmpty())
        @include('student.partials.empty', ['message' => 'No exams have been scheduled for your section.'])
    @else
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr><th class="px-4 py-3">Exam</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Dates</th><th class="px-4 py-3">Centre</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($exams as $e)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $e->name }}<div class="text-xs text-gray-400">{{ $e->group_name }}</div></td>
                            <td class="px-4 py-3">{{ $e->type_name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $fmt($e->start_date) }} → {{ $fmt($e->end_date) }}</td>
                            <td class="px-4 py-3">{{ $e->exam_center ?: '—' }}</td>
                            <td class="px-4 py-3">@include('student.partials.badge', ['status' => $e->state])</td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('student.exams.show', $e->id) }}" class="text-indigo-600 hover:underline">Date sheet</a>
                                @if($e->is_published)<a href="{{ route('student.results.show', $e->id) }}" class="text-green-600 hover:underline">Result</a>@endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
