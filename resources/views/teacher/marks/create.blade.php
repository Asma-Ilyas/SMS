@extends('layouts.app')
@section('title', 'Enter Marks')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Enter Marks', 'subtitle' => 'Select an exam and subject'])

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[280px]">
            <label class="text-xs text-gray-500">Exam · Class · Subject</label>
            <select name="task" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($tasks->where('locked', false) as $x)
                    <option value="{{ $x->key }}" @selected($task && $task->key === $x->key)>{{ $x->exam->name }} — {{ $x->pair->section_label }} — {{ $x->pair->subject_name }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Load</button>
    </form>

    @if($tasks->isEmpty())
        @include('teacher.partials.empty', ['message' => 'There are no exams for your classes yet.'])
    @endif

    @if($task)
        @if($students->isEmpty())
            @include('teacher.partials.empty', ['message' => 'No active students in this class.'])
        @else
        <form method="POST" action="{{ route('teacher.marks.store') }}" class="bg-white rounded-xl border shadow-sm">
            @csrf
            <input type="hidden" name="task" value="{{ $task->key }}">
            <div class="px-5 py-4 border-b">
                <div class="font-semibold text-gray-800">{{ $task->exam->name }} · {{ $task->pair->subject_name }}</div>
                <div class="text-xs text-gray-500">{{ $task->pair->section_label }} · Maximum <strong>{{ $task->max }}</strong> · Passing <strong>{{ $task->pass }}</strong> · Leave blank for absent</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Roll</th><th class="px-4 py-2">Student</th><th class="px-4 py-2">Marks (/{{ $task->max }})</th><th class="px-4 py-2">Remarks</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($students as $s)
                            @php $m = $marks[$s->id] ?? null; @endphp
                            <tr>
                                <td class="px-4 py-2 text-gray-500">{{ $s->roll_number ?: '—' }}</td>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ $s->first_name }} {{ $s->last_name }}<div class="text-xs text-gray-400">{{ $s->admission_number }}</div></td>
                                <td class="px-4 py-2"><input type="number" step="1" min="0" max="{{ $task->max }}" name="marks[{{ $s->id }}]"
                                    value="{{ old("marks.{$s->id}", $m->marks_obtained ?? '') }}" class="w-28 rounded-lg border-gray-300 text-sm"></td>
                                <td class="px-4 py-2"><input type="text" maxlength="255" name="remarks[{{ $s->id }}]" value="{{ old("remarks.{$s->id}", $m->remarks ?? '') }}" class="w-full min-w-[160px] rounded-lg border-gray-300 text-sm"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t flex justify-end"><button class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">💾 Save Marks</button></div>
        </form>
        @endif
    @endif
</div>
@endsection
