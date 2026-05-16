@extends('layouts.app')
@section('title', 'Assign Subject')
@section('content')
<div class="max-w-3xl mx-auto py-8"><div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-5"><h2 class="text-2xl font-bold text-white">Assign Subject to Teacher & Class</h2></div>
    <form method="POST" action="{{ route('admin.subject-assignments.store') }}" class="px-6 py-6 space-y-4">@csrf
        <div><label class="block font-semibold">Class *</label><select name="class_id" class="w-full border rounded p-2" required><option value="">Select Class</option>@foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->full_name }}</option>@endforeach</select></div>
        <div><label class="block font-semibold">Subject *</label><select name="subject_id" class="w-full border rounded p-2" required><option value="">Select Subject</option>@foreach($subjects as $sub)<option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>@endforeach</select></div>
        <div><label class="block font-semibold">Teacher *</label><select name="teacher_id" class="w-full border rounded p-2" required><option value="">Select Teacher</option>@foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->full_name }}</option>@endforeach</select></div>
        <div><label class="block font-semibold">Academic Session</label><select name="academic_session_id" class="w-full border rounded p-2"><option value="">Optional</option>@foreach($sessions as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
        <div><label class="block font-semibold">Max Weekly Periods</label><input type="number" name="max_weekly_periods" min="0" class="w-full border rounded p-2"></div>
        <div><label class="block font-semibold">Term *</label><select name="term" class="w-full border rounded p-2"><option value="full_year">Full Year</option><option value="first">First Term</option><option value="second">Second Term</option><option value="third">Third Term</option></select></div>
        <div><label class="block font-semibold">Notes</label><textarea name="notes" rows="2" class="w-full border rounded p-2"></textarea></div>
        <div class="flex justify-end space-x-3 pt-4"><a href="{{ route('admin.subject-assignments.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Assign</button></div>
    </form>
</div></div>
@endsection