@extends('layouts.app')
@section('title', 'Exam Types')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Exam Types</h1>
        <a href="{{ route('admin.exam-types.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Exam Type</a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th>Name</th><th>Code</th><th>Sort Order</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($examTypes as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->code ?? '-' }}</td>
                    <td>{{ $type->sort_order }}</td>
                    <td><span class="px-2 py-1 text-xs rounded-full {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.exam-types.edit', $type) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.exam-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Delete this exam type?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4">No exam types found. <a href="{{ route('admin.exam-types.create') }}" class="text-blue-600">Create one</a></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-2">{{ $examTypes->links() }}</div>
    </div>
</div>
@endsection