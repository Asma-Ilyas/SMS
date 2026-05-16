@extends('layouts.app')
@section('title', 'Edit Exam Group')
@section('content')
<div class="max-w-md mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b"><h1 class="text-2xl font-bold">Edit Exam Group</h1></div>
        <form method="POST" action="{{ route('admin.exam-groups.update', $examGroup) }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div><label>Name *</label><input type="text" name="name" value="{{ old('name', $examGroup->name) }}" required class="w-full border rounded px-3 py-2"></div>
            <div><label>Code</label><input type="text" name="code" value="{{ old('code', $examGroup->code) }}" class="w-full border rounded px-3 py-2"></div>
            <div><label>Description</label><textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $examGroup->description) }}</textarea></div>
            <div><label>Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', $examGroup->sort_order) }}" class="w-full border rounded px-3 py-2"></div>
            <div><label><input type="checkbox" name="is_active" value="1" {{ $examGroup->is_active ? 'checked' : '' }}> Active</label></div>
            <div class="flex justify-end space-x-3"><a href="{{ route('admin.exam-groups.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button></div>
        </form>
    </div>
</div>
@endsection