@extends('layouts.app')
@section('title', 'Exam Reports')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Exam Reports', 'subtitle' => 'Performance analysis of your subjects'])

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[280px]">
            <label class="text-xs text-gray-500">Exam · Class · Subject</label>
            <select name="task" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($tasks as $x)
                    <option value="{{ $x->key }}" @selected($task && $task->key === $x->key)>{{ $x->exam->name }} — {{ $x->pair->section_label }} — {{ $x->pair->subject_name }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Analyse</button>
    </form>

    @if($task && $stats)
        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
            @foreach([['Students', $stats['students'], 'text-gray-800'], ['Entered', $stats['entered'], 'text-gray-800'], ['Absent', $stats['absent'], 'text-red-600'], ['Average', $stats['avg'] !== null ? $stats['avg'].'%' : '—', 'text-indigo-600'], ['Highest', $stats['high'] !== null ? $stats['high'].'%' : '—', 'text-green-600'], ['Lowest', $stats['low'] !== null ? $stats['low'].'%' : '—', 'text-red-600'], ['Passed', $stats['passed'], 'text-green-600'], ['Pass rate', $stats['pass_rate'] !== null ? $stats['pass_rate'].'%' : '—', 'text-indigo-600']] as [$l, $v, $c])
                <div class="bg-white rounded-xl border shadow-sm p-4 text-center"><div class="text-xs uppercase text-gray-500">{{ $l }}</div><div class="text-2xl font-bold {{ $c }}">{{ $v }}</div></div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Grade distribution</h3>
                @forelse($dist as $g => $n)
                    <div class="flex items-center gap-3 text-sm mb-2">
                        <div class="w-10 font-semibold">{{ $g }}</div>
                        <div class="flex-1 bg-gray-100 rounded-full h-3"><div class="h-3 rounded-full bg-indigo-500" style="width: {{ $stats['entered'] ? round($n / $stats['entered'] * 100) : 0 }}%"></div></div>
                        <div class="w-10 text-right text-gray-600">{{ $n }}</div>
                    </div>
                @empty <p class="text-sm text-gray-500">No marks entered yet.</p> @endforelse
            </div>
            <div class="bg-white rounded-xl border shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Class average across exams</h3>
                @forelse($trend as $t)
                    <div class="flex items-center gap-3 text-sm mb-2">
                        <div class="w-40 truncate text-gray-600">{{ $t->name }}</div>
                        <div class="flex-1 bg-gray-100 rounded-full h-3"><div class="h-3 rounded-full {{ $t->avg_pct >= 50 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, round($t->avg_pct)) }}%"></div></div>
                        <div class="w-14 text-right text-gray-600">{{ round($t->avg_pct, 1) }}%</div>
                    </div>
                @empty <p class="text-sm text-gray-500">No history yet.</p> @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Student results — {{ $task->exam->name }} · {{ $task->pair->subject_name }}</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Roll</th><th class="px-4 py-2">Student</th><th class="px-4 py-2">Marks</th><th class="px-4 py-2">%</th><th class="px-4 py-2">Grade</th><th class="px-4 py-2">Result</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($rows->sortByDesc('pct') as $r)
                        <tr>
                            <td class="px-4 py-2 text-gray-500">{{ $r->student->roll_number ?: '—' }}</td>
                            <td class="px-4 py-2 font-medium">{{ $r->student->first_name }} {{ $r->student->last_name }}</td>
                            <td class="px-4 py-2">{{ $r->obtained ?? 'Absent' }} / {{ $r->max }}</td>
                            <td class="px-4 py-2">{{ $r->pct !== null ? $r->pct.'%' : '—' }}</td>
                            <td class="px-4 py-2">{{ $r->grade }}</td>
                            <td class="px-4 py-2">@if($r->passed !== null) @include('teacher.partials.badge', ['status' => $r->passed ? 'pass' : 'fail']) @else — @endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
