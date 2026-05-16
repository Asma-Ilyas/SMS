@extends('layouts.app')

@section('title', 'Edit Certificate Type')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Edit Certificate Type: {{ $certificateType->title }}</h2>
        <form method="POST" action="{{ route('admin.certificates.update-type', $certificateType) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="block font-medium">Title *</label>
                <input type="text" name="title" value="{{ old('title', $certificateType->title) }}" class="w-full border rounded px-3 py-2" required>
                @error('title')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="block font-medium">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $certificateType->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="block font-medium">Current Template</label>
                @if($certificateType->template_file)
                    <div class="flex items-center space-x-2">
                        <span class="text-green-600">File uploaded</span>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remove_template" value="1">
                            <span class="ml-2 text-sm text-red-600">Remove existing template</span>
                        </label>
                    </div>
                @else
                    <span class="text-gray-500">No template uploaded</span>
                @endif
            </div>
            <div class="mb-3">
                <label class="block font-medium">Replace / New Template (optional)</label>
                <input type="file" name="template_file" accept=".pdf,.jpg,.jpeg,.png">
                @error('template_file')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div class="mb-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $certificateType->is_active) ? 'checked' : '' }}>
                    <span class="ml-2">Active</span>
                </label>
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 bg-gray-300 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection