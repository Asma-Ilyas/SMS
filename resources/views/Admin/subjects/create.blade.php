@extends('layouts.app')
@section('title', 'Add Subject')
@section('content')
<div class="max-w-2xl mx-auto py-8"><div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Add Subject</h2>
    <form method="POST" action="{{ route('admin.subjects.store') }}">@csrf
        <div class="mb-3"><label>Name *</label><input type="text" name="name" class="w-full border rounded p-2" required></div>
        <div class="mb-3"><label>Code *</label><input type="text" name="code" class="w-full border rounded p-2" required></div>
        <div class="mb-3"><label>Description</label><textarea name="description" rows="3" class="w-full border rounded p-2"></textarea></div>
        <div class="mb-3"><label>Type *</label><select name="type" class="w-full border rounded p-2"><option value="theory">Theory</option><option value="practical">Practical</option><option value="elective">Elective</option></select></div>
        <div class="mb-3 flex items-center"><input type="checkbox" name="is_active" value="1" checked> <span class="ml-2">Active</span></div>
        <div class="flex justify-end space-x-2"><a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a><button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save</button></div>
    </form>
</div></div>
@endsection