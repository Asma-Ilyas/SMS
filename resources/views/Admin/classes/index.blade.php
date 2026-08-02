@extends('layouts.app')
@section('title', 'Classes')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Classes</h1>
            <a href="{{ route('admin.classes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">+ New Class</a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Elective Track</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sections</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($classes as $class)
                    <tr>
                        <td class="px-6 py-4">{{ $class->academicSession->name }}</td>
                        <td class="px-6 py-4">{{ $class->grade->name }}</td>
                        <td class="px-6 py-4">{{ $class->stream->name }}</td>
                        <td class="px-6 py-4">{{ $class->electiveTrack->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $class->capacity }}</td>
                        <td class="px-6 py-4">{{ $class->class_sections_count }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.classes.show', $class) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            <a href="{{ route('admin.classes.edit', $class) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            <button type="button" class="text-green-600 hover:text-green-900" onclick="openPromoteClassModal({{ $class->id }}, '{{ addslashes($class->full_name) }}')">Promote All</button>
                            <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Delete this class? Also deletes its sections?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No classes found. <a href="{{ route('admin.classes.create') }}" class="text-indigo-600">Add first class</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $classes->links() }}</div>
    </div>
</div>

{{-- Promote Whole Class Modal (unchanged) --}}
<div id="promoteClassModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium">Promote Class: <span id="promoteClassName"></span></h3>
            <form id="promoteClassForm" method="POST">
                @csrf
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Target Class</label>
                    <select name="target_class_id" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                        <option value="">Select Target Class</option>
                        @foreach($allClasses as $targetClass)
                            <option value="{{ $targetClass->id }}">{{ $targetClass->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Target Academic Session (Optional)</label>
                    <select name="target_academic_session_id" class="mt-1 block w-full border rounded-md px-3 py-2">
                        <option value="">Same as current</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400" onclick="closePromoteClassModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Promote All</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPromoteClassModal(classId, className) {
        document.getElementById('promoteClassName').innerText = className;
        document.getElementById('promoteClassForm').action = `/admin/classes/${classId}/promote`;
        document.getElementById('promoteClassModal').classList.remove('hidden');
    }
    function closePromoteClassModal() {
        document.getElementById('promoteClassModal').classList.add('hidden');
    }
</script>
@endsection