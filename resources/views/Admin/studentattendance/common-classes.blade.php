@extends('layouts.app')
@section('title', 'Common Subject Classes')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📚 Common Subject Classes</h1>
                    <p class="text-gray-500 mt-1">Combine multiple sections for the same subject in one room</p>
                </div>
                <a href="{{ route('admin.studentattendance.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">← Back</a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">{{ session('success') }}</div>
        @endif

        {{-- Step 1: Select Class Section --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🎯 Select Class Section</h2>
            <form method="GET" action="{{ route('admin.studentattendance.common-classes') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Section</label>
                    <select name="class_section_id" class="w-full rounded-lg border-gray-300" onchange="this.form.submit()">
                        <option value="">-- Select Section --</option>
                        @foreach($classSections as $section)
                        <option value="{{ $section->id }}" {{ $selectedSection && $selectedSection->id == $section->id ? 'selected' : '' }}>
                            {{ $section->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    @if($selectedSection)
                    <a href="{{ route('admin.studentattendance.common-classes') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Clear Selection</a>
                    @endif
                </div>
            </form>
            
            @if($selectedSection)
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-blue-800">
                    <strong>Selected Section:</strong> {{ $selectedSection->full_name }}
                    <span class="ml-4 text-sm text-blue-600">
                        ({{ $selectedSections->count() }} sections in this class)
                    </span>
                </p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($selectedSections as $sec)
                    <span class="px-2 py-1 text-xs rounded-full {{ $sec->id == $selectedSection->id ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800' }}">
                        {{ $sec->section_name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Step 2: Create/Edit Common Class (Only if section selected) --}}
        @if($selectedSection)
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">➕ Create Common Class for {{ $selectedSection->full_name }}</h2>
            <form method="POST" action="{{ route('admin.studentattendance.common-classes.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="class_section_id" value="{{ $selectedSection->id }}">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <select name="subject_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Name *</label>
                    <input type="text" name="name" required placeholder="e.g., Computer Lab Batch A" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room *</label>
                    <select name="room_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Select Room</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }} (Capacity: {{ $room->capacity }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sections Included *</label>
                    <select name="class_section_ids[]" required multiple class="w-full rounded-lg border-gray-300" size="4">
                        @foreach($selectedSections as $section)
                        <option value="{{ $section->id }}" {{ $section->id == $selectedSection->id ? 'selected' : '' }}>
                            {{ $section->section_name }} ({{ $section->full_name }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl to select multiple. The selected section is pre-selected.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Additional notes about this combined class"></textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg transition">
                        Create Common Class
                    </button>
                </div>
            </form>
        </div>

        {{-- Existing Common Classes for this section --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">📋 Common Classes for {{ $selectedSection->full_name }}</h2>
                <span class="text-sm text-gray-500">{{ $commonClasses->count() }} class(es)</span>
            </div>
            
            @if($commonClasses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($commonClasses as $class)
                <div class="border rounded-xl p-4 hover:shadow-lg transition">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $class->name }}</h3>
                            <p class="text-sm text-indigo-600">{{ $class->subject->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">{{ $class->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <div>🏠 Room: {{ $class->room->name ?? 'N/A' }}</div>
                        <div>📚 Sections: 
                            @php
                                // FIXED: class_section_ids is already an array from the model cast
                                $sectionIds = $class->class_section_ids ?? [];
                                $sectionNames = \App\Models\ClassSection::whereIn('id', $sectionIds)->pluck('section_name')->implode(', ');
                            @endphp
                            {{ $sectionNames ?: 'N/A' }}
                        </div>
                        @if($class->description)
                        <div class="text-gray-500 text-xs mt-2">{{ $class->description }}</div>
                        @endif
                    </div>
                    <div class="flex gap-2 mt-3 pt-3 border-t">
                        <a href="{{ route('admin.studentattendance.common-classes.edit', $class->id) }}" 
                           class="flex-1 text-center text-indigo-600 hover:text-indigo-800 text-sm py-1 border border-indigo-200 rounded hover:bg-indigo-50 transition">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="{{ route('admin.studentattendance.common-classes.destroy', $class->id) }}" 
                              onsubmit="return confirm('Delete this common class?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center text-red-600 hover:text-red-800 text-sm py-1 border border-red-200 rounded hover:bg-red-50 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No common classes for this section</h3>
                <p class="mt-1 text-sm text-gray-500">Create a common class using the form above.</p>
            </div>
            @endif
        </div>
        @else
        {{-- If no section selected, show all common classes --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-xl font-bold text-gray-800">📋 All Common Classes</h2>
                <p class="text-sm text-gray-500 mt-1">Select a section above to filter or create new common classes</p>
            </div>
            
            @if($commonClasses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($commonClasses as $class)
                <div class="border rounded-xl p-4 hover:shadow-lg transition">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $class->name }}</h3>
                            <p class="text-sm text-indigo-600">{{ $class->subject->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">{{ $class->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <div>🏠 Room: {{ $class->room->name ?? 'N/A' }}</div>
                        <div>📚 Sections: 
                            @php
                                // FIXED: class_section_ids is already an array from the model cast
                                $sectionIds = $class->class_section_ids ?? [];
                                $sectionNames = \App\Models\ClassSection::whereIn('id', $sectionIds)->pluck('section_name')->implode(', ');
                            @endphp
                            {{ $sectionNames ?: 'N/A' }}
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3 pt-3 border-t">
                        <a href="{{ route('admin.studentattendance.common-classes.edit', $class->id) }}" 
                           class="flex-1 text-center text-indigo-600 hover:text-indigo-800 text-sm py-1 border border-indigo-200 rounded hover:bg-indigo-50 transition">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="{{ route('admin.studentattendance.common-classes.destroy', $class->id) }}" 
                              onsubmit="return confirm('Delete this common class?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center text-red-600 hover:text-red-800 text-sm py-1 border border-red-200 rounded hover:bg-red-50 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No common classes created yet</h3>
                <p class="mt-1 text-sm text-gray-500">Select a section above to create common classes.</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Legend / Help --}}
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
            <h3 class="font-bold text-gray-800 mb-3">📖 About Common Subject Classes</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="font-medium text-blue-800">🎯 Purpose</p>
                    <p class="text-blue-700 text-xs mt-1">Combine multiple sections for the same subject in one room (e.g., Computer Lab)</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <p class="font-medium text-green-800">📋 How it works</p>
                    <p class="text-green-700 text-xs mt-1">Select a class section, choose sections to combine, assign a subject and room</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <p class="font-medium text-yellow-800">⚡ Benefits</p>
                    <p class="text-yellow-700 text-xs mt-1">Optimize room usage, share resources, efficient scheduling</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection