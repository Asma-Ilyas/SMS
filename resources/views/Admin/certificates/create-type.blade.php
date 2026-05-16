@extends('layouts.app')

@section('title', 'Create Certificate Type')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Create Certificate Type</h2>
        <form method="POST" action="{{ route('admin.certificates.store-type') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block font-medium">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
                @error('title')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="block font-medium">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="block font-medium">Template File (PDF/Image)</label>
                <input type="file" name="template_file" accept=".pdf,.jpg,.jpeg,.png">
                <p class="text-xs text-gray-500">Optional template can be used as fallback for distributions.</p>
                @error('template_file')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <span class="ml-2">Active (can be distributed)</span>
                </label>
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 bg-gray-300 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save Certificate Type</button>
            </div>
        </form>
    </div>
</div>
@endsection