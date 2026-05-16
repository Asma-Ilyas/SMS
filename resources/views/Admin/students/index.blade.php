{{-- resources/views/admin/students/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Students List')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Students</h1>
        <a href="{{ route('admin.students.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add New Student
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $student->admission_number }}</td>
                    <td class="px-6 py-3">{{ $student->full_name }}</td>
                    <td class="px-6 py-3">{{ $student->class->full_name ?? $student->class->name }}</td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $student->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $student->status }}
                        </span>
                    </td>
                    <td class="px-6 py-3 space-x-2">
                        <a href="{{ route('admin.students.show', $student) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="text-yellow-600 hover:underline">Edit</a>
                        <button type="button" class="text-green-600 hover:underline" onclick="openPromoteModal({{ $student->id }}, '{{ addslashes($student->full_name) }}')">Promote</button>
                        <a href="{{ route('admin.students.transfer-out.form', $student) }}" class="text-red-600 hover:underline">Transfer Out</a>
                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this student?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No students found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>

{{-- Promote Modal --}}
<div id="promoteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium">Promote Student: <span id="studentName"></span></h3>
            <form id="promoteForm" method="POST">
                @csrf
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Target Class <span class="text-red-500">*</span></label>
                    <select name="target_class_id" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                        <option value="">Select Class</option>
                        @foreach($allClasses as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->full_name }}</option>
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
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Promote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPromoteModal(studentId, studentName) {
        document.getElementById('studentName').innerText = studentName;
        document.getElementById('promoteForm').action = `/admin/students/${studentId}/promote`;
        document.getElementById('promoteModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('promoteModal').classList.add('hidden');
    }
</script>
@endsection