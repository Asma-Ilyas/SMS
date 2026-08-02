@extends('layouts.app')
@section('title', 'Teacher Permissions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.studentattendance.permissions') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Permissions</a>
        <h1 class="text-3xl font-bold text-gray-800 mt-2">👨‍🏫 {{ $teacher->first_name }} {{ $teacher->last_name }}</h1>
        <p class="text-gray-500">Permissions for this teacher</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mark Attendance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Approve Leave</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($permissions as $perm)
                    <tr class="hover:bg-gray-50">
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
                        <td class="px-6 py-4 text-center">
                            <form method="POST" action="{{ route('admin.studentattendance.permissions.destroy', $perm->id) }}" 
                                  onsubmit="return confirm('Remove this permission?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">🗑️ Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-gray-500">No permissions for this teacher</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection