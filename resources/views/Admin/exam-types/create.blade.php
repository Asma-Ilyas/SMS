@extends('layouts.app')
@section('title', 'Add Exam Type')
@section('content')
<div class="max-w-md mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h1 class="text-2xl font-bold">Add Exam Type</h1>
        </div>
        <form method="POST" action="{{ route('admin.exam-types.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block font-medium">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
                @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block font-medium">Code</label>
                <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded px-3 py-2">
                <p class="text-sm text-gray-500">Optional short code (e.g., TEST, MONTHLY)</p>
            </div>
            <div>
                <label class="block font-medium">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" value="1" checked> <span>Active</span>
                </label>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.exam-types.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection