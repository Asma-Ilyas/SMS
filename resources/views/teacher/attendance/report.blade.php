@extends('layouts.app')
@section('title', 'Attendance Report')
@section('content')
@php $codes = ['present'=>['P','text-green-600'],'absent'=>['A','text-red-600'],'late'=>['L','text-yellow-600'],'half_day'=>['H','text-yellow-600'],'leave'=>['V','text-blue-600'],'on_duty'=>['D','text-indigo-600'],'holiday'=>['O','text-gray-400']]; @endphp
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Attendance Report', 'subtitle' => 'Monthly attendance by class and subject'])

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[240px]">
            <label class="text-xs text-gray-500">Class · Subject</label>
            <select name="pair" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($pairs as $p)<option value="{{ $p->key }}" @selected($sel && $sel->key === $p->key)>{{ $p->label }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500">Month</label>
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" class="block mt-1 rounded-lg border-gray-300 text-sm">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Show</button>
        @if($sel)<a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="px-4 py-2 bg-white border rounded-lg text-sm hover:bg-gray-50">⬇ CSV</a>@endif
    </form>

    @if($sel)
        @if($dates->isEmpty())
            @include('teacher.partials.empty', ['message' => 'No attendance was recorded for this class in '.$month->format('F Y').'.'])
        @else
            <div class="text-xs text-gray-500">P present · A absent · L late · H half day · V leave · D on duty · O holiday</div>
            <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-3 py-2 text-left">Roll</th><th class="px-3 py-2 text-left">Student</th>
                            @foreach($dates as $d)<th class="px-2 py-2 text-center">{{ substr($d, 8, 2) }}</th>@endforeach
                            <th class="px-3 py-2">P</th><th class="px-3 py-2">A</th><th class="px-3 py-2">L</th><th class="px-3 py-2">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($students as $s)
                            @php $sm = $summary[$s->id] ?? null; @endphp
                            <tr>
                                <td class="px-3 py-2 text-gray-500">{{ $s->roll_number ?: '—' }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800 whitespace-nowrap">{{ $s->first_name }} {{ $s->last_name }}</td>
                                @foreach($dates as $d)
                                    @php $c = $codes[$matrix[$s->id][$d] ?? ''] ?? ['·','text-gray-300']; @endphp
                                    <td class="px-2 py-2 text-center font-semibold {{ $c[1] }}">{{ $c[0] }}</td>
                                @endforeach
                                <td class="px-3 py-2 text-center">{{ $sm['present'] ?? 0 }}</td>
                                <td class="px-3 py-2 text-center">{{ $sm['absent'] ?? 0 }}</td>
                                <td class="px-3 py-2 text-center">{{ $sm['late'] ?? 0 }}</td>
                                <td class="px-3 py-2 text-center font-semibold {{ ($sm['pct'] ?? 0) >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $sm ? $sm['pct'].'%' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
@endsection
