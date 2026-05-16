@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-teal-700 px-6 py-5 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Subject Details</h2>
            <a href="{{ route('admin.subjects.index') }}" class="bg-white text-green-600 px-4 py-2 rounded-lg">Back</a>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Name:</strong> {{ $subject->name }}</div>
                <div><strong>Code:</strong> {{ $subject->code }}</div>
                <div><strong>Type:</strong> {{ ucfirst($subject->type) }}</div>
                <div><strong>Status:</strong> 
                    <span class="px-2 py-1 text-xs rounded {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $subject->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="col-span-2"><strong>Description:</strong><br>{{ $subject->description ?? '-' }}</div>
                <div><strong>Created:</strong> {{ $subject->created_at->format('d-m-Y H:i') }}</div>
                <div><strong>Last Updated:</strong> {{ $subject->updated_at->format('d-m-Y H:i') }}</div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.subjects.edit', $subject) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Edit Subject</a>
                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" onsubmit="return confirm('Delete this subject?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection