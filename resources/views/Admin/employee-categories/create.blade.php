@extends('layouts.app')
@section('title', 'Add Employee Category')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Add Employee Category</h1>
        <form method="POST" action="{{ route('admin.employee-categories.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block font-medium">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Arrival Time (HH:MM)</label>
                        <input type="time" name="arrival_time" value="{{ old('arrival_time') }}" class="w-full border rounded px-3 py-2">
                        <p class="text-xs text-gray-500">Leave empty for flexible timing</p>
                    </div>
                    <div>
                        <label class="block font-medium">Departure Time (HH:MM)</label>
                        <input type="time" name="departure_time" value="{{ old('departure_time') }}" class="w-full border rounded px-3 py-2">
                        <p class="text-xs text-gray-500">Leave empty for flexible timing</p>
                    </div>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2">Active</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <a href="{{ route('admin.employee-categories.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection