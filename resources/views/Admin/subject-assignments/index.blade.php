@extends('layouts.app')
@section('title', 'Subject & Teacher Assignments')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl shadow-lg mb-6">
        <div class="px-6 py-5 flex justify-between"><h1 class="text-2xl font-bold text-white">Subject-Teacher-Class Assignments</h1><a href="{{ route('admin.subject-assignments.create') }}" class="bg-white text-indigo-600 px-4 py-2 rounded">+ New Assignment</a></div>
    </div>
    @if(session('success'))<div class="bg-green-100 p-3 mb-4">{{ session('success') }}</div>@endif
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y"><thead class="bg-gray-50"><tr><th class="px-6 py-3">Class</th><th>Subject</th><th>Teacher</th><th>Session</th><th>Periods/Wk</th><th>Term</th><th class="text-right">Action</th></tr></thead>
        <tbody>@forelse($assignments as $a)
            <tr><td class="px-6 py-4">{{ $a->class_name }}</td><td>{{ $a->subject_name }}</td><td>{{ $a->first_name }} {{ $a->last_name }}</td><td>{{ $a->session_name ?? '-' }}</td><td>{{ $a->max_weekly_periods }}</td><td>{{ ucfirst($a->term) }}</td>
            <td class="text-right"><form action="{{ route('admin.subject-assignments.destroy', $a->id) }}" method="POST">@csrf @method('DELETE')<button class="text-red-600">Remove</button></form></td></tr>
        @empty<tr><td colspan="7" class="text-center py-8 text-gray-500">No assignments yet.</td></tr>@endforelse
        </tbody></table>
    </div>
</div>
@endsection