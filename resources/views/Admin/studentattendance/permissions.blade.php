@extends('layouts.app')
@section('title', 'Manage Teacher Permissions')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">👨‍🏫 Teacher Attendance Permissions</h1>
                    <p class="text-gray-500 mt-1">Control which teachers can mark attendance and approve leave</p>
                </div>
                <a href="{{ route('admin.studentattendance.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">← Back</a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">{{ session('error') }}</div>
        @endif

        {{-- Tabs: Individual Assign & Bulk Assign --}}
        <div x-data="{ tab: 'individual' }" class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex border-b border-gray-200 mb-6">
                <button @click="tab = 'individual'" 
                        class="px-4 py-2 font-medium text-sm transition"
                        :class="tab === 'individual' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                    ➕ Individual Assignment
                </button>
                <button @click="tab = 'bulk'" 
                        class="px-4 py-2 font-medium text-sm transition"
                        :class="tab === 'bulk' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                    👥 Bulk Assignment (All Teachers)
                </button>
                <button @click="tab = 'remove'" 
                        class="px-4 py-2 font-medium text-sm transition"
                        :class="tab === 'remove' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700'">
                    🗑️ Bulk Remove
                </button>
            </div>

            {{-- Individual Assignment Tab --}}
            <div x-show="tab === 'individual'">
                <form method="POST" action="{{ route('admin.studentattendance.permissions.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                        <select name="teacher_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section</label>
                        <select name="class_section_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Section</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject (Optional)</label>
                        <select name="subject_id" class="w-full rounded-lg border-gray-300">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mark Attendance</label>
                        <select name="can_mark_attendance" class="w-full rounded-lg border-gray-300">
                            <option value="1">✅ Yes</option>
                            <option value="0">❌ No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approve Leave</label>
                        <select name="can_approve_leave" class="w-full rounded-lg border-gray-300">
                            <option value="0">❌ No</option>
                            <option value="1">✅ Yes</option>
                        </select>
                    </div>
                    <div class="md:col-span-5">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg transition">Assign Permission</button>
                    </div>
                </form>
            </div>

            {{-- Bulk Assignment Tab --}}
            <div x-show="tab === 'bulk'">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-blue-800 text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span>This will assign permissions to <strong>ALL teachers</strong> for the selected section/subject. <br>Total teachers: <strong>{{ $teachers->count() }}</strong></span>
                    </p>
                </div>
                
                <form method="POST" action="{{ route('admin.studentattendance.permissions.bulk') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Section</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject (Optional)</label>
                        <select name="subject_id" class="w-full rounded-lg border-gray-300">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mark Attendance</label>
                        <select name="can_mark_attendance" class="w-full rounded-lg border-gray-300">
                            <option value="1">✅ Yes</option>
                            <option value="0">❌ No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approve Leave</label>
                        <select name="can_approve_leave" class="w-full rounded-lg border-gray-300">
                            <option value="0">❌ No</option>
                            <option value="1">✅ Yes</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Assign to ALL {{ $teachers->count() }} Teachers
                        </button>
                    </div>
                </form>
            </div>

            {{-- Bulk Remove Tab --}}
            <div x-show="tab === 'remove'">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-red-800 text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span><strong>Warning:</strong> This will remove ALL permissions for the selected section/subject. This action cannot be undone.</span>
                    </p>
                </div>
                
                <form method="POST" action="{{ route('admin.studentattendance.permissions.bulk-remove') }}" 
                      onsubmit="return confirm('Are you sure you want to remove ALL permissions for this section? This action cannot be undone.')" 
                      class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    @method('DELETE')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class Section *</label>
                        <select name="class_section_id" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Section</option>
                            @foreach($classSections as $section)
                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject (Optional)</label>
                        <select name="subject_id" class="w-full rounded-lg border-gray-300">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove All Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Existing Permissions Table --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">📋 Existing Permissions</h2>
                <div class="flex gap-2">
                    <span class="text-sm text-gray-500">{{ $permissions->sum(fn($p) => $p->count()) }} total permissions</span>
                    <span class="text-sm text-gray-500">|</span>
                    <span class="text-sm text-gray-500">{{ $teachers->count() }} teachers</span>
                </div>
            </div>
            
            @if($permissions->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mark</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Approve</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned By</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($permissions as $teacherId => $perms)
                            @php 
                                $firstPerm = $perms->first();
                                $teacherName = $firstPerm->teacher->first_name ?? 'Unknown';
                                $teacherLastName = $firstPerm->teacher->last_name ?? '';
                                $rowspan = $perms->count();
                            @endphp
                            @foreach($perms as $index => $perm)
                            <tr class="hover:bg-gray-50">
                                @if($index === 0)
                                <td class="px-6 py-4" rowspan="{{ $rowspan }}">
                                    <div class="font-medium text-gray-800">{{ $teacherName }} {{ $teacherLastName }}</div>
                                    <a href="{{ route('admin.studentattendance.permissions.teacher', $teacherId) }}" 
                                       class="text-xs text-indigo-600 hover:underline">view all</a>
                                </td>
                                @endif
                                <td class="px-6 py-4">{{ $perm->classSection->full_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $perm->subject->name ?? 'All Subjects' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($perm->can_mark_attendance)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Yes</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">❌ No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($perm->can_approve_leave)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Yes</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">❌ No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $perm->assigner->name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.studentattendance.permissions.destroy', $perm->id) }}" 
                                          onsubmit="return confirm('Remove this permission?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm flex items-center justify-center gap-1 mx-auto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions assigned</h3>
                <p class="mt-1 text-sm text-gray-500">Assign permissions to teachers using the form above.</p>
            </div>
            @endif
        </div>

        {{-- Legend / Help --}}
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-gray-800 mb-3">📖 Understanding Permissions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="font-medium text-green-800">✅ Mark Attendance</p>
                    <p class="text-green-700 text-xs mt-1">Teacher can mark attendance for this section/subject</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="font-medium text-blue-800">✅ Approve Leave</p>
                    <p class="text-blue-700 text-xs mt-1">Teacher can approve leave requests for this section</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <p class="font-medium text-yellow-800">👥 Bulk Assign</p>
                    <p class="text-yellow-700 text-xs mt-1">Assign permissions to ALL teachers at once for a section</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-submit form when selecting section in bulk remove
    document.querySelectorAll('form').forEach(form => {
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                // You can add auto-submit logic here if needed
            });
        });
    });
</script>
@endsection