@extends('layouts.app')
@section('title', 'Employee Categories')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Employee Categories</h1>
        <a href="{{ route('admin.employee-categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Category</a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th>Name</th><th>Code</th><th>Description</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->code ?? '-' }}</td>
                    <td>{{ $cat->description ?? '-' }}</td>
                    <td>{{ $cat->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.employee-categories.edit', $cat) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.employee-categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Delete category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4">No categories found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $categories->links() }}
    </div>
</div>
@endsection