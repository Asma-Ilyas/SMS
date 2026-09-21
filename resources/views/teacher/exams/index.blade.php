@extends('layouts.app')
@section('title', 'My Exams')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Exams', 'subtitle' => 'Exams for the classes and subjects you teach'])

    @if($groups->isEmpty())
        @include('teacher.partials.empty', ['message' => 'No exams found for your classes.'])
    @else
        @foreach($groups as $g)
            <div class="bg-white rounded-xl border shadow-sm">
                <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="font-semibold text-gray-800">{{ $g->exam->name }} <span class="text-xs text-gray-400">{{ $g->exam->type_name }}</span></h2>
                        <div class="text-xs text-gray-500">{{ $g->tasks->first()->pair->section_label }} · {{ $fmt($g->exam->start_date) }} → {{ $fmt($g->exam->end_date) }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        @include('teacher.partials.badge', ['status' => $g->state])
                        @if($g->exam->is_published) @include('teacher.partials.badge', ['status' => 'published']) @endif
                        <a href="{{ route('teacher.exams.show', $g->exam->id) }}" class="text-sm text-indigo-600 hover:underline">Details</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">My subject</th><th class="px-4 py-2">Max / Pass</th><th class="px-4 py-2">Marks entered</th><th></th></tr></thead>
                        <tbody class="divide-y">
                            @foreach($g->tasks as $x)
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $x->pair->subject_name }}</td>
                                    <td class="px-4 py-2">{{ $x->max }} / {{ $x->pass }}</td>
                                    <td class="px-4 py-2">{{ $x->entered }} / {{ $x->total }}
                                        <div class="w-32 bg-gray-100 h-1.5 rounded mt-1"><div class="h-1.5 rounded bg-indigo-500" style="width: {{ $x->total ? min(100, round($x->entered / $x->total * 100)) : 0 }}%"></div></div></td>
                                    <td class="px-4 py-2 text-right">
                                        @if($x->locked) <span class="text-xs text-gray-400">Locked</span>
                                        @else <a class="text-indigo-600 hover:underline" href="{{ route('teacher.marks.create', ['task' => $x->key]) }}">Enter marks</a> @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
