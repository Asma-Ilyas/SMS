@extends('layouts.app')
@section('title', 'Timetable Dashboard')

@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .timetable-cell { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .timetable-cell:hover { transform: translateY(-2px); border-color: #6366f1; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1), 0 4px 6px -4px rgba(99, 102, 241, 0.1); }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">📅 Timetable Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                    @if(isset($activeTiming) && $activeTiming)
                        Current Session: <span class="font-semibold text-gray-700">{{ $activeTiming->session_name ?? 'Active Session' }}</span>
                        @if($activeTiming->is_active ?? false)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ring-1 ring-inset ring-green-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>Active
                            </span>
                        @endif
                    @else
                        <span class="text-yellow-600">⚠️ No active timing session found</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex rounded-lg shadow-sm bg-white border p-1 gap-1">
                    <a href="{{ route('admin.timetable-reports.class') }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 rounded-md hover:bg-gray-50 transition">📚 Class View</a>
                    <a href="{{ route('admin.timetable-reports.section') }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 rounded-md hover:bg-gray-50 transition">📋 Section View</a>
                    <a href="{{ route('admin.timetable-reports.teacher') }}" class="px-3 py-1.5 text-xs font-medium text-gray-700 rounded-md hover:bg-gray-50 transition">👨‍🏫 Teacher View</a>
                </div>
                @if(isset($activeTiming) && $activeTiming)
                    <a href="{{ route('admin.timetable-reports.print', request()->all()) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium bg-white border text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 transition">🖨 Print</a>
                    <a href="{{ route('admin.timetable-reports.export', request()->all()) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium bg-white border text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 transition">⬇ Export</a>
                @endif

                {{-- Bulk Edit Section Link --}}
                <div class="flex items-center gap-2 border-l pl-3 ml-1">
                    <label class="text-xs font-semibold text-gray-600">Bulk Edit:</label>
                    <select id="sectionEditSelect" class="rounded-md border-gray-300 text-sm py-1.5">
                        <option value="">-- Select Section --</option>
                        @if(isset($classSections) && $classSections->count() > 0)
                            @foreach($classSections as $section)
                                <option value="{{ $section->id }}">{{ $section->full_name ?? $section->section_name ?? 'Section' }}</option>
                            @endforeach
                        @endif
                    </select>
                    <button onclick="editSection()" 
                            class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                        Edit Full Section
                    </button>
                </div>
            </div>
        </div>

        {{-- Controls Panel (Filters) --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-8">
            <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Filter by Class or Teacher</h2>
                <form method="GET" action="{{ route('admin.timetable-reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Session</label>
                        <select name="timing_id" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @if(isset($timings) && $timings->count() > 0)
                                @foreach($timings as $t)
                                    <option value="{{ $t->id }}" @selected(isset($selectedTiming) && $t->id == $selectedTiming->id)>
                                        {{ $t->session_name ?? 'Session' }} {{ ($t->is_active ?? false) ? '✅' : '' }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">No timings available</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Class / Section</label>
                        <select name="class_section_id" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Sections</option>
                            @if(isset($classSections) && $classSections->count() > 0)
                                @foreach($classSections as $section)
                                    <option value="{{ $section->id }}" @selected(request('class_section_id') == $section->id)>
                                        {{ $section->full_name ?? $section->section_name ?? 'Section' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Teacher</label>
                        <select name="teacher_id" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Teachers</option>
                            @if(isset($teachers) && $teachers->count() > 0)
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" @selected(request('teacher_id') == $teacher->id)>
                                        {{ $teacher->full_name ?? $teacher->name ?? 'Teacher' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="sm:col-span-3 flex justify-end pt-2">
                        @if(isset($selectedTiming))
                            <a href="{{ route('admin.timetable-reports.index', ['timing_id' => $selectedTiming->id]) }}" class="text-xs font-medium text-gray-500 hover:text-gray-800 underline transition">
                                Clear Active Filters
                            </a>
                        @else
                            <a href="{{ route('admin.timetable-reports.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800 underline transition">
                                Clear Active Filters
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl shadow-md p-5 flex flex-col justify-between text-white border border-slate-700">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Engine Controls</h2>
                    <p class="text-xs text-slate-300 leading-relaxed">Auto-scheduling removes manual clashes based on room layouts and resource capacity configurations.</p>
                </div>
                @if(isset($activeTiming) && $activeTiming)
                <form action="{{ route('admin.timetable-reports.generate') }}" method="POST" onsubmit="return confirm('Generate new timetable? This will permanently wipe existing active entries.')" class="mt-4">
                    @csrf
                    <input type="hidden" name="timing_id" value="{{ $activeTiming->id }}">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-400 transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                        ⚡ Auto-Generate Engine
                    </button>
                </form>
                @else
                <div class="mt-4 p-3 bg-yellow-500/20 rounded-lg border border-yellow-500/30 text-yellow-300 text-xs">
                    <p>⚠️ No active timing session found. Please create a timing session first.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl text-sm flex items-start gap-3 shadow-sm animate-fade-in">
                <span class="text-emerald-500 text-lg leading-none">✓</span>
                <div><span class="font-semibold">Success!</span> {{ session('success') }}</div>
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="text-amber-500 text-lg leading-none">⚠️</span>
                    <div>
                        <span class="font-semibold">Warning Notification</span>
                        <p class="mt-0.5 text-amber-800">{{ session('warning') }}</p>
                    </div>
                </div>
                @if(session('gen_errors'))
                    <ul class="mt-3 ml-8 list-disc text-xs text-amber-800 space-y-1 bg-amber-100/50 p-3 rounded-lg border border-amber-200/60">
                        @foreach(session('gen_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-900 rounded-xl text-sm shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="text-red-500 text-lg leading-none">✕</span>
                    <div>
                        <span class="font-semibold">Error!</span>
                        <p class="mt-0.5 text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Periods / Day</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ isset($timeSlots) ? $timeSlots->where('type','period')->count() : 0 }}</p>
                </div>
                <div class="p-2.5 bg-indigo-50 rounded-lg text-indigo-600 text-xl font-bold">⏱️</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Sections</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ $classSections->count() ?? 0 }}</p>
                </div>
                <div class="p-2.5 bg-emerald-50 rounded-lg text-emerald-600 text-xl font-bold">🏫</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Entries</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ $entries->count() ?? 0 }}</p>
                </div>
                <div class="p-2.5 bg-sky-50 rounded-lg text-sky-600 text-xl font-bold">📝</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Recess Breaks</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ isset($timeSlots) ? $timeSlots->where('type','break')->count() : 0 }}</p>
                </div>
                <div class="p-2.5 bg-amber-50 rounded-lg text-amber-600 text-xl font-bold">☕</div>
            </div>
        </div>

        {{-- Weekly Timetable Matrix --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="font-bold text-gray-800 tracking-tight flex items-center gap-2">📅 Master Weekly Matrix</span>
                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded font-medium">Mon - Fri</span>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-100/70">
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b">Time Schedule</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b">Monday</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b">Tuesday</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b">Wednesday</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-b">Thursday</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-b">Friday</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    @if(isset($timeSlots) && $timeSlots->count() > 0)
                        @foreach($timeSlots as $slot)
                            @php 
                                $isBreak = $slot->type === 'break';
                                $isActivity = $slot->type === 'activity';
                                $rowClass = $isBreak ? 'bg-amber-50/60' : ($isActivity ? 'bg-emerald-50/40' : 'hover:bg-gray-50/50 transition-colors'); 
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="px-4 py-4 font-semibold text-gray-900 border-r align-top whitespace-nowrap bg-gray-50/30">
                                    <span class="text-sm tracking-tight block">{{ $slot->start_time }} – {{ $slot->end_time }}</span>
                                    <span class="inline-block mt-1 text-[11px] px-1.5 py-0.5 font-medium rounded tracking-wide uppercase {{ $isBreak ? 'bg-amber-100 text-amber-800' : ($isActivity ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $slot->label ?? 'Period' }}
                                    </span>
                                </td>
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                                @endphp
                                @foreach($days as $day)
                                    @php 
                                        $entry = isset($groupedEntries[$day]) ? $groupedEntries[$day]->firstWhere('time_slot_id', $slot->id) : null; 
                                    @endphp
                                    <td class="px-4 py-3 border-r last:border-r-0 align-top min-w-[160px]" data-day="{{ $loop->index + 1 }}" data-slot="{{ $slot->id }}">
                                        @if($slot->type === 'period')
                                            @if($entry)
                                                <div class="timetable-cell group relative bg-white border border-gray-200 rounded-xl p-3 shadow-sm hover:shadow-md transition duration-200" data-id="{{ $entry->id }}">
                                                    <div class="font-bold text-gray-900 text-[13px] leading-snug subject tracking-tight mb-1">{{ $entry->subject->name ?? 'N/A' }}</div>
                                                    <div class="text-xs font-medium text-gray-600 flex items-center gap-1 teacher mb-0.5">
                                                        <span>👨‍🏫</span> {{ optional($entry->teacher)->full_name ?? optional($entry->teacher)->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-[11px] text-gray-500 font-medium section">
                                                        📋 {{ optional($entry->classSection)->full_name ?? optional($entry->classSection)->section_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-[11px] text-gray-400 mt-1 border-t pt-1 border-gray-100 font-mono room">
                                                        Room: {{ optional($entry->room)->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="mt-2.5 flex items-center justify-end">
                                                        <button type="button" class="edit-btn inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded transition">
                                                            ✎ Edit
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center justify-center h-16 border border-dashed border-gray-200 rounded-xl text-gray-300 text-xs italic bg-gray-50/20">
                                                    Unassigned
                                                </div>
                                            @endif
                                        @else
                                            <div class="py-4 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded {{ $isBreak ? 'bg-amber-100/80 text-amber-800' : 'bg-emerald-100/80 text-emerald-800' }} uppercase tracking-wider">
                                                    {{ ucfirst($slot->type) }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2">No time slots found. Please create time slots for this timing session.</p>
                                @if(isset($activeTiming) && $activeTiming)
                                    <a href="{{ route('admin.slots.index', ['schoolTiming' => $activeTiming->id]) }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                        ➕ Add Time Slots
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all animate-fade-in">
            <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden transform scale-95 transition-transform duration-300">
                <div class="px-6 py-4 bg-slate-50 border-b flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-800 tracking-tight">Modify Matrix Node</h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                <form id="editForm" class="p-6" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editId">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5 tracking-wider">Subject Allocation</label>
                            <select name="subject_id" id="editSubject" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @if(isset($subjects) && $subjects->count() > 0)
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5 tracking-wider">Assigned Lecturer</label>
                            <select name="teacher_id" id="editTeacher" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @if(isset($teachers) && $teachers->count() > 0)
                                    @foreach($teachers as $tch)
                                        <option value="{{ $tch->id }}">{{ $tch->full_name ?? $tch->name ?? 'Teacher' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5 tracking-wider">Room Matrix</label>
                            <select name="room_id" id="editRoom" class="w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @if(isset($rooms) && $rooms->count() > 0)
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }} (Capacity: {{ $room->capacity ?? 'N/A' }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm tracking-wide transition">Save Layout Changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- JavaScript --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Edit button click handler
                document.body.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.edit-btn');
                    if (!editBtn) return;

                    const cell = editBtn.closest('.timetable-cell');
                    if (!cell) {
                        alert('Internal error: Could not locate timetable cell.');
                        return;
                    }

                    const entryId = cell.dataset.id;
                    if (!entryId) {
                        alert('No entry ID found. Unable to edit.');
                        return;
                    }

                    const editDataUrl = '{{ route("admin.timetable-reports.edit-entry", ":id") }}'.replace(':id', entryId);
                    
                    fetch(editDataUrl)
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            document.getElementById('editId').value = data.id;
                            document.getElementById('editSubject').value = data.subject_id;
                            document.getElementById('editTeacher').value = data.teacher_id;
                            document.getElementById('editRoom').value = data.room_id;
                            
                            const updateUrl = '{{ route("admin.timetable-reports.update-entry", ":id") }}'.replace(':id', entryId);
                            document.getElementById('editForm').action = updateUrl;
                            
                            document.getElementById('editModal').classList.remove('hidden');
                            document.getElementById('editModal').classList.add('flex');
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Failed to load entry data. Check browser console.');
                        });
                });

                // Edit form submission
                const editForm = document.getElementById('editForm');
                if (editForm) {
                    editForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);

                        fetch(this.action, {
                            method: 'POST',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                                'Accept': 'application/json' 
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Update failed: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Error updating timetable entry.');
                        });
                    });
                }

                // Edit section function
                window.editSection = function() {
                    const sectionId = document.getElementById('sectionEditSelect').value;
                    if (!sectionId) {
                        alert('Please select a section first.');
                        return;
                    }
                    window.location.href = '{{ route("admin.timetable-reports.edit-section", ":id") }}'.replace(':id', sectionId);
                };
            });

            function closeModal() {
                const modal = document.getElementById('editModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        </script>

    </div>
</div>
@endsection