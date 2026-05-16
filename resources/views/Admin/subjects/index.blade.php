@extends('layouts.app')
@section('title', 'Subjects')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-green-600 to-teal-700 rounded-xl shadow-lg mb-6">
        <div class="px-6 py-5 flex justify-between"><h1 class="text-2xl font-bold text-white">Subjects</h1><a href="{{ route('admin.subjects.create') }}" class="bg-white text-green-600 px-4 py-2 rounded">+ Add Subject</a></div>
    </div>
    @if(session('success'))<div class="bg-green-100 p-3 mb-4">{{ session('success') }}</div>@endif
    <div class="bg-white rounded shadow overflow-x-auto"><table class="min-w-full"><thead><tr><th class="p-3">Code</th><th>Name</th><th>Type</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
        <tbody>@foreach($subjects as $s)
        <tr><td class="p-3">{{ $s->code }}</td><td>{{ $s->name }}</td><td>{{ ucfirst($s->type) }}</td><td><span class="px-2 py-1 text-xs rounded {{ $s->is_active ? 'bg-green-100' : 'bg-red-100' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td class="text-right space-x-2"><a href="{{ route('admin.subjects.edit', $s) }}" class="text-indigo-600">Edit</a><form action="{{ route('admin.subjects.destroy', $s) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('Delete?')">Delete</button></form></td></tr>
        @endforeach</tbody>
    </table></div>{{ $subjects->links() }}
</div>
@endsection