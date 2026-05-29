@extends('layouts.app')
@section('title', 'Employee Categories')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Employee Categories</h1>
        <a href="{{ route('admin.employee-categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Category</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Arrival Time</th>
                    <th>Departure Time</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="px-4 py-2">{{ $category->name }}</td>
                    <td class="px-4 py-2">{{ $category->code ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $category->arrival_time ?? 'Flexible' }}</td>
                    <td class="px-4 py-2">{{ $category->departure_time ?? 'Flexible' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('admin.employee-categories.edit', $category) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.employee-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <td><td colspan="6" class="px-4 py-8 text-center text-gray-500">No categories found. <a href="{{ route('admin.employee-categories.create') }}">Create one</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</div>
@endsection