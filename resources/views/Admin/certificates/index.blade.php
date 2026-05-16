@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Certificates</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.certificates.create-type') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Create Certificate Type</a>
            <a href="{{ route('admin.certificates.history') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Distribution History</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Total Distributed</th>
                    <th class="px-6 py-3 text-left">Template</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificateTypes as $type)
                <tr>
                    <td class="px-6 py-3">{{ $type->title }}</td>
                    <td class="px-6 py-3">{{ $type->distributions_count }}</td>
                    <td class="px-6 py-3">
                        @if($type->template_file)
                            ✅ Template uploaded
                        @else
                            ❌ No template
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 space-x-2">
                        <a href="{{ route('admin.certificates.edit-type', $type) }}" class="text-yellow-600">Edit</a>
                        <form action="{{ route('admin.certificates.destroy-type', $type) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this certificate type?')" class="text-red-600">Delete</button>
                        </form>
                        <a href="{{ route('admin.certificates.distribute', $type) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Distribute</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center">No certificate types found. Create one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection