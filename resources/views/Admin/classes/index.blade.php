@extends('layouts.app')

@section('title', 'Classes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Classes</h1>
        <a href="{{ route('admin.classes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add New Class
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stream</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Elective</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $class)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $class->session->name ?? 'N/A' }}</td>
                    <td class="px-6 py-3">{{ $class->grade->name ?? 'N/A' }}</td>
                    <td class="px-6 py-3">{{ $class->stream->name ?? 'N/A' }}</td>
                    <td class="px-6 py-3">{{ $class->electiveTrack->name ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $class->section ?? '-' }}</td>
                    <td class="px-6 py-3">{{ $class->students_count ?? 0 }}</td>
                    <td class="px-6 py-3 space-x-2">
                        <a href="{{ route('admin.classes.edit', $class) }}" class="text-yellow-600 hover:underline">Edit</a>
                        <button type="button" class="text-green-600 hover:underline" onclick="openPromoteClassModal({{ $class->id }}, '{{ addslashes($class->full_name) }}')">Promote All</button>
                        <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this class?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No classes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $classes->links() }}
    </div>
</div>

{{-- Promote Whole Class Modal --}}
<div id="classPromoteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium">Promote Entire Class: <span id="className"></span></h3>
            <form id="classPromoteForm" method="POST">
                @csrf
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Target Class <span class="text-red-500">*</span></label>
                    <select name="target_class_id" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                        <option value="">Select Target Class</option>
                        @foreach($allClasses as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Target Academic Session (Optional)</label>
                    <select name="target_academic_session_id" class="mt-1 block w-full border rounded-md px-3 py-2">
                        <option value="">Keep current session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400" onclick="closeClassModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Promote All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPromoteClassModal(classId, className) {
        document.getElementById('className').innerText = className;
        document.getElementById('classPromoteForm').action = `/admin/classes/${classId}/promote-all`;
        document.getElementById('classPromoteModal').classList.remove('hidden');
    }
    function closeClassModal() {
        document.getElementById('classPromoteModal').classList.add('hidden');
    }
</script>
@endsection