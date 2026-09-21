@extends('layouts.app')
@section('title', 'Mark Attendance')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'Mark Attendance', 'subtitle' => 'Choose a class, subject and date'])

    @if(session('success'))
        <div class="p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <ul class="list-disc pl-5">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[240px]">
            <label class="text-xs text-gray-500">Class · Subject</label>
            <select name="pair" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">— select —</option>
                @foreach($pairs as $p)<option value="{{ $p->key }}" @selected($sel && $sel->key === $p->key)>{{ $p->label }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500">Date</label>
            <input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}" class="block mt-1 rounded-lg border-gray-300 text-sm">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Load students</button>
    </form>

    @if($pairs->isEmpty())
        @include('teacher.partials.empty', ['message' => 'You have no assigned classes, so you cannot mark attendance.'])
    @endif

    @if($sel)
        @if($date->isWeekend())
            <div class="p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">⚠ {{ $date->format('l') }} is a weekend — make sure you want to mark attendance for this date.</div>
        @endif

        @if($students->isEmpty())
            @include('teacher.partials.empty', ['message' => 'No active students in this class.'])
        @else
        <form method="POST" action="{{ route('teacher.attendance.store') }}" class="bg-white rounded-xl border shadow-sm">
            @csrf
            <input type="hidden" name="pair" value="{{ $sel->key }}">
            <input type="hidden" name="date" value="{{ $date->toDateString() }}">

            <div class="px-5 py-4 border-b flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="font-semibold text-gray-800">{{ $sel->label }}</div>
                    <div class="text-xs text-gray-500">{{ $date->format('l, d M Y') }} · {{ $students->count() }} students @if($existing->isNotEmpty()) · <span class="text-indigo-600">already marked — editing</span> @endif</div>
                </div>
                <div class="flex gap-2 text-xs">
                    <button type="button" onclick="setAll('present')" class="px-3 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100">All Present</button>
                    <button type="button" onclick="setAll('absent')" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">All Absent</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-2">Roll</th><th class="px-4 py-2">Student</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Details</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($students as $s)
                            @php
                                $e  = $existing[$s->id] ?? null;
                                $st = old("attendance.{$s->id}.status", $e->status ?? 'present');
                            @endphp
                            <tr x-data="{ s: '{{ $st }}' }">
                                <td class="px-4 py-2 text-gray-500">{{ $s->roll_number ?: '—' }}</td>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ $s->first_name }} {{ $s->last_name }}<div class="text-xs text-gray-400">{{ $s->admission_number }}</div></td>
                                <td class="px-4 py-2">
                                    <select name="attendance[{{ $s->id }}][status]" x-model="s" data-status class="rounded-lg border-gray-300 text-sm">
                                        @foreach(['present'=>'Present','absent'=>'Absent','late'=>'Late','half_day'=>'Half day','leave'=>'Leave','on_duty'=>'On duty'] as $v => $l)
                                            <option value="{{ $v }}" @selected($st === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>

                                    {{-- Show the approval state of a leave that was already saved --}}
                                    @if($e && $e->status === 'leave')
                                        <div class="mt-1 text-xs {{ $e->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                                            {{ $e->is_approved ? 'Leave approved' : 'Leave awaiting admin approval' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <input type="number" min="0" max="600" name="attendance[{{ $s->id }}][late_minutes]" x-show="s === 'late'" placeholder="min late"
                                               value="{{ old("attendance.{$s->id}.late_minutes", $e && $e->late_minutes ? $e->late_minutes : '') }}" class="w-24 rounded-lg border-gray-300 text-sm">
                                        <select name="attendance[{{ $s->id }}][half_day_type]" x-show="s === 'half_day'" class="rounded-lg border-gray-300 text-sm">
                                            @foreach(['morning','afternoon','custom'] as $ht)<option value="{{ $ht }}" @selected(old("attendance.{$s->id}.half_day_type", $e->half_day_type ?? '') === $ht)>{{ ucfirst($ht) }}</option>@endforeach
                                        </select>
                                        <input type="text" maxlength="255" name="attendance[{{ $s->id }}][remarks]"
                                               :placeholder="s === 'leave' ? 'Reason for leave (required)' : 'Remarks / reason'"
                                               :required="s === 'leave'"
                                               value="{{ old("attendance.{$s->id}.remarks", $e->remarks ?? '') }}" class="flex-1 min-w-[160px] rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div x-show="s === 'leave'" class="mt-1 text-xs text-blue-600">The admin will be asked to approve this leave.</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t flex justify-end">
                <button class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">💾 Save Attendance</button>
            </div>
        </form>
        <script>
            function setAll(v) {
                document.querySelectorAll('select[data-status]').forEach(function (el) {
                    el.value = v; el.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        </script>
        @endif
    @endif
</div>
@endsection