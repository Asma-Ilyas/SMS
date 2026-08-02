@extends('layouts.app')

@section('title', 'Student Management')

@push('styles')
<style>
    .action-btn {
        transition: all 0.2s ease;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        font-weight: 600;
    }
    .action-btn:hover {
        transform: scale(1.1);
        text-decoration: none;
    }
    .action-btn-view {
        color: #4f46e5;
        background: #eef2ff;
    }
    .action-btn-view:hover {
        background: #c7d2fe;
        color: #4338ca;
    }
    .action-btn-edit {
        color: #d97706;
        background: #fffbeb;
    }
    .action-btn-edit:hover {
        background: #fde68a;
        color: #b45309;
    }
    .action-btn-delete {
        color: #dc2626;
        background: #fef2f2;
    }
    .action-btn-delete:hover {
        background: #fca5a5;
        color: #b91c1c;
    }
    .action-btn-attendance {
        color: #2563eb;
        background: #eff6ff;
    }
    .action-btn-attendance:hover {
        background: #93c5fd;
        color: #1d4ed8;
    }
    .action-btn-exam {
        color: #7c3aed;
        background: #f5f3ff;
    }
    .action-btn-exam:hover {
        background: #ddd6fe;
        color: #6d28d9;
    }
    .action-btn-fee {
        color: #059669;
        background: #ecfdf5;
    }
    .action-btn-fee:hover {
        background: #a7f3d0;
        color: #047857;
    }
    .stat-icon {
        font-size: 20px;
        line-height: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-indigo-600">👨‍🎓</span>
                Student Management
            </h1>
            <p class="text-sm text-gray-500 mt-1">Manage all students, their records, and academic progress</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition shadow-sm">
                <span class="mr-2">➕</span> Add New Student
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Students</p>
                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Student::count() }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-xl">
                    👨‍🎓
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Students</p>
                    <p class="text-2xl font-bold text-green-600">{{ \App\Models\Student::where('status', 'Active')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-xl">
                    ✅
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Inactive</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Student::where('status', 'Inactive')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-xl">
                    ⏸️
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Avg Attendance</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format(\App\Models\StudentAttendance::where('status', 'present')->count() / max(1, \App\Models\StudentAttendance::count()) * 100, 1) }}%</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-xl">
                    📊
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, admission number..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Class Section</label>
                <select name="class_section_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Sections</option>
                    @foreach($classSections as $section)
                        <option value="{{ $section->id }}" {{ request('class_section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->full_name ?? $section->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                🔍 Filter
            </button>
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition text-sm font-medium">
                🔄 Reset
            </a>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Admission No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Attendance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $students->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($student->profile_photo)
                                        <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->full_name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $student->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $student->email ?? 'No email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $student->admission_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $student->current_class }}</td>
                            <td class="px-4 py-3 text-center text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $student->gender == 'Male' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ $student->gender }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-medium {{ $student->attendance_percentage >= 80 ? 'text-green-600' : ($student->attendance_percentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($student->attendance_percentage, 1) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($student->status == 'Active')
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.students.show', $student->id) }}" 
                                       class="action-btn action-btn-view" 
                                       title="View Student">
                                        👁️
                                    </a>
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.students.edit', $student->id) }}" 
                                       class="action-btn action-btn-edit" 
                                       title="Edit Student">
                                        ✏️
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <button onclick="deleteStudent({{ $student->id }}, '{{ addslashes($student->full_name) }}')" 
                                            class="action-btn action-btn-delete" 
                                            title="Delete Student">
                                        🗑️
                                    </button>
                                    
                                    <!-- Attendance Button -->
                                    <a href="{{ route('admin.students.attendance', $student->id) }}" 
                                       class="action-btn action-btn-attendance" 
                                       title="View Attendance">
                                        📅
                                    </a>
                                    
                                    <!-- Exam Results Button -->
                                    <a href="{{ route('admin.students.exam-results', $student->id) }}" 
                                       class="action-btn action-btn-exam" 
                                       title="View Exam Results">
                                        📊
                                    </a>
                                    
                                    <!-- Fee Details Button -->
                                    <a href="{{ route('admin.students.fee-details', $student->id) }}" 
                                       class="action-btn action-btn-fee" 
                                       title="View Fee Details">
                                        💰
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <div class="text-3xl mb-2">👨‍🎓</div>
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $students->links() }}
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                ⚠️
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Delete Student</h3>
            <p class="text-sm text-gray-500 mb-4" id="deleteMessage">Are you sure you want to delete this student?</p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteStudent(id, name) {
        document.getElementById('deleteMessage').textContent = `Are you sure you want to delete ${name}? This action cannot be undone.`;
        document.getElementById('deleteForm').action = `/admin/students/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endpush
@endsection