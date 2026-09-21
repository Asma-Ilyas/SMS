@extends('layouts.app')
@section('title', 'Report Cards')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Report Cards', 'subtitle' => 'Published results for students of your classes'])

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="text-xs text-gray-500">Class</label>
            <select name="section_id" required onchange="this.form.submit()" class="block mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($sections as $s)<option value="{{ $s->id }}" @selected($sectionId == $s->id)>{{ $s->label }}</option>@endforeach
            </select>
        </div>
        @if($sectionId)
            <div>
                <label class="text-xs text-gray-500">Published exam</label>
                <select name="exam_id" required class="block mt-1 rounded-lg border-gray-300 text-sm">
                    <option value="">— select —</option>
                    @foreach($exams as $e)<option value="{{ $e->id }}" @selected($examId == $e->id)>{{ $e->name }}</option>@endforeach
                </select>
            </div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
        @endif
    </form>

    @if($sectionId && $exams->isEmpty())
        @include('teacher.partials.empty', ['message' => 'No published exams for this class yet.'])
    @endif

    @if($examId)
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Roll</th><th class="px-4 py-2">Student</th><th class="px-4 py-2">Total</th><th class="px-4 py-2">%</th><th class="px-4 py-2">Grade</th><th class="px-4 py-2">Rank</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($students as $s)
                        @php $r = $results[$s->id] ?? null; @endphp
                        <tr>
                            <td class="px-4 py-2 text-gray-500">{{ $s->roll_number ?: '—' }}</td>
                            <td class="px-4 py-2 font-medium">{{ $s->first_name }} {{ $s->last_name }}</td>
                            <td class="px-4 py-2">{{ $r ? rtrim(rtrim(number_format($r->total_marks,2),'0'),'.').' / '.rtrim(rtrim(number_format($r->total_max_marks,2),'0'),'.') : '—' }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $r && $r->percentage !== null ? number_format($r->percentage,1).'%' : '—' }}</td>
                            <td class="px-4 py-2">{{ $r->grade ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $r->rank_in_class ?? '—' }}</td>
                            <td class="px-4 py-2 text-right"><a class="text-indigo-600 hover:underline" href="{{ route('teacher.report-card.show', [$examId, $s->id]) }}">Report card</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
