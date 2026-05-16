@extends('layouts.app')
@section('title', 'Exam Groups')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manage Exam Groups</h1>
        <a href="{{ route('admin.exam-groups.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Group</a>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th>Name</th><th>Code</th><th>Sort Order</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr>
                    <td>{{ $group->name }}</td><td>{{ $group->code ?? '-' }}</td><td>{{ $group->sort_order }}</td>
                    <td><span class="px-2 py-1 text-xs rounded-full {{ $group->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $group->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.exam-groups.edit', $group) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.exam-groups.destroy', $group) }}" method="POST" class="inline" onsubmit="return confirm('Delete group?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <td><td colspan="5" class="text-center py-4">No groups found. </td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $groups->links() }}
    </div>
</div>
@endsection