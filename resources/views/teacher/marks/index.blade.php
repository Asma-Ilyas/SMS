@extends('layouts.app')
@section('title', 'Marks Overview')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Marks Overview', 'subtitle' => 'Progress of marks entry for your subjects'])

    <div class="flex gap-3"><a href="{{ route('teacher.marks.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">✏️ Enter Marks</a></div>

    @foreach([['Open for entry', $open, false], ['Published (locked)', $closed, true]] as [$title, $list, $locked])
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">{{ $title }} <span class="text-xs text-gray-400">({{ $list->count() }})</span></div>
            @if($list->isEmpty())
                <div class="p-6 text-sm text-gray-500">Nothing here.</div>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Exam</th><th class="px-4 py-2">Class</th><th class="px-4 py-2">Subject</th><th class="px-4 py-2">Max / Pass</th><th class="px-4 py-2">Progress</th><th></th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($list as $x)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $x->exam->name }}<div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($x->exam->start_date)->format('d M Y') }}</div></td>
                                <td class="px-4 py-2">{{ $x->pair->section_label }}</td>
                                <td class="px-4 py-2">{{ $x->pair->subject_name }}</td>
                                <td class="px-4 py-2">{{ $x->max }} / {{ $x->pass }}</td>
                                <td class="px-4 py-2">{{ $x->entered }} / {{ $x->total }}
                                    <div class="w-28 bg-gray-100 h-1.5 rounded mt-1"><div class="h-1.5 rounded {{ $x->entered >= $x->total && $x->total > 0 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $x->total ? min(100, round($x->entered / $x->total * 100)) : 0 }}%"></div></div></td>
                                <td class="px-4 py-2 text-right space-x-3">
                                    <a class="text-gray-600 hover:underline" href="{{ route('teacher.exam-reports.index', ['task' => $x->key]) }}">Report</a>
                                    @if(!$locked)<a class="text-indigo-600 hover:underline" href="{{ route('teacher.marks.create', ['task' => $x->key]) }}">Enter</a>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
</div>
@endsection
