@extends('layouts.app')
@section('title', 'Add Category')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-md mx-auto bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">New Employee Category</h1>
        <form method="POST" action="{{ route('admin.employee-categories.store') }}">
            @csrf
            <div class="mb-3">
                <label>Name *</label>
                <input type="text" name="name" required class="w-full border rounded p-2">
                @error('name')<div class="text-red-600">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label>Code</label>
                <input type="text" name="code" class="w-full border rounded p-2">
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" rows="3" class="w-full border rounded p-2"></textarea>
            </div>
            <div class="mb-3">
                <label><input type="checkbox" name="is_active" value="1" checked> Active</label>
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.employee-categories.index') }}" class="px-4 py-2 bg-gray-300 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection