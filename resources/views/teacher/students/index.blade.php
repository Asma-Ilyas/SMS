@extends('layouts.app')
@section('title', 'My Students')
@section('content')
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Students', 'subtitle' => 'Students in the classes you teach'])

    <form method="GET" class="bg-white rounded-xl border shadow-sm p-4 flex flex-wrap items-end gap-3">
        <div>
            <label class="text-xs text-gray-500">Class</label>
            <select name="section_id" class="block mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">All my classes</option>
                @foreach($sections as $s)<option value="{{ $s->id }}" @selected(request('section_id') == $s->id)>{{ $s->label }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500">Status</label>
            <select name="status" class="block mt-1 rounded-lg border-gray-300 text-sm">
                <option value="">Any</option>
                <option value="Active" @selected(request('status') == 'Active')>Active</option>
                <option value="Inactive" @selected(request('status') == 'Inactive')>Inactive</option>
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs text-gray-500">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, admission #, roll #" class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Filter</button>
    </form>

    @if($sections->isEmpty())
        @include('teacher.partials.empty', ['message' => 'You have no assigned classes yet.'])
    @else
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Roll</th><th class="px-4 py-3">Adm #</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Class</th><th class="px-4 py-3">Gender</th><th class="px-4 py-3">Parent</th><th class="px-4 py-3">Status</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @forelse($students as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $s->roll_number ?: '—' }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $s->admission_number }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $s->first_name }} {{ $s->last_name }}</td>
                            <td class="px-4 py-2">{{ $s->grade_name }}-{{ $s->section_name }}</td>
                            <td class="px-4 py-2">{{ $s->gender }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $s->father_name }}<div class="text-xs text-gray-400">{{ $s->father_phone }}</div></td>
                            <td class="px-4 py-2">@include('teacher.partials.badge', ['status' => $s->status])</td>
                            <td class="px-4 py-2 text-right"><a href="{{ route('teacher.students.show', $s->id) }}" class="text-indigo-600 hover:underline">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator)<div>{{ $students->links() }}</div>@endif
    @endif
</div>
@endsection
