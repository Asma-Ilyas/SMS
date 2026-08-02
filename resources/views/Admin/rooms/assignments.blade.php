@extends('layouts.app')
@section('title', 'Room Assignments')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header with session info and actions --}}
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Room Assignments
            </h1>
            <p class="text-gray-500 mt-1">
                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Session: <strong class="text-gray-900">{{ $session->session_name ?? 'Active Session' }}</strong>
                @if(isset($session) && $session->is_active)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            {{-- Session switcher --}}
            <form method="GET" class="relative">
                <select name="timing_id" onchange="this.form.submit()" class="appearance-none bg-white border border-gray-300 rounded-lg py-2 pl-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($allTimings as $t)
                        <option value="{{ $t->id }}" {{ $t->id === $session->id ? 'selected':'' }}>
                            {{ $t->session_name }}{{ $t->is_active ? ' ✓' : '' }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </form>
            
            {{-- Auto-assign button --}}
            <form action="{{ route('admin.rooms.auto-assign') }}" method="POST">
                @csrf
                <input type="hidden" name="timing_id" value="{{ $session->id }}">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition" onclick="return confirm('Auto-assign rooms to ALL unassigned sections? Existing MANUAL assignments are kept.')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Auto-Assign All
                </button>
            </form>
            <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Rooms
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg text-sm">
            ⚠️ {{ session('warning') }}
        </div>
    @endif

    {{-- Auto-assignment results summary --}}
    @if(session('assignment_results'))
        @php $r = session('assignment_results'); $s = $r['summary']; @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-3">
                <h3 class="text-white font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Auto-Assignment Results
                </h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 text-center mb-4">
                    <div><div class="text-2xl font-bold text-indigo-600">{{ $s['total_sections'] ?? 0 }}</div><div class="text-xs text-gray-500">Total Sections</div></div>
                    <div><div class="text-2xl font-bold text-green-600">{{ $s['assigned'] ?? 0 }}</div><div class="text-xs text-gray-500">Assigned</div></div>
                    <div><div class="text-2xl font-bold text-red-600">{{ count($s['over_capacity'] ?? []) }}</div><div class="text-xs text-gray-500">Over Capacity</div></div>
                    <div><div class="text-2xl font-bold text-yellow-600">{{ count($s['no_room'] ?? []) }}</div><div class="text-xs text-gray-500">No Room Found</div></div>
                    <div><div class="text-2xl font-bold text-blue-600">{{ $s['skipped_manual'] ?? 0 }}</div><div class="text-xs text-gray-500">Skipped (Manual)</div></div>
                    <div><div class="text-2xl font-bold text-gray-600">{{ $s['rooms_free'] ?? 0 }}</div><div class="text-xs text-gray-500">Rooms Still Free</div></div>
                </div>
            </div>
        </div>
    @endif

    {{-- Statistics cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['totalRooms'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Total Rooms</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['freeRooms'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Free Rooms</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['assignedCount'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Assigned</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 transition-all hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-50 rounded-xl">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['overCapacity'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Over Capacity</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: Assignments table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">
                        <svg class="inline w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                        Current Assignments
                    </h3>
                    <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $assignments->count() }} records</span>
                </div>
                <div class="p-4 bg-white border-b border-gray-100">
                    <form method="GET" class="flex flex-wrap gap-3 items-end">
                        <input type="hidden" name="timing_id" value="{{ $session->id }}">
                        <div class="flex-1 min-w-[160px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Search Section</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Section name…" class="pl-9 w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <div class="w-40">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fit Status</label>
                            <select name="fit_status" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All</option>
                                @foreach(['perfect','comfortable','tight','over_capacity'] as $fs)
                                    <option value="{{ $fs }}" {{ request('fit_status') === $fs ? 'selected':'' }}>{{ ucfirst(str_replace('_', ' ', $fs)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Filter</button>
                            <a href="{{ route('admin.rooms.assignments', ['timing_id'=>$session->id]) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Strength</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($assignments as $a)
                            <tr class="hover:bg-gray-50 transition {{ $a->fit_status === 'over_capacity' ? 'border-l-4 border-l-red-500' : ($a->fit_status === 'tight' ? 'border-l-4 border-l-yellow-400' : '') }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ optional($a->section)->section_name ?? '—' }}</div>
                                    <div class="text-sm text-gray-500">{{ optional(optional($a->section)->class)->grade->name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ optional($a->room)->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ optional($a->room)->room_number ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold">{{ $a->section_strength ?? 0 }}</td>
                                <td class="px-6 py-4 text-center">{{ $a->room_capacity ?? 0 }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $fitColors = [
                                            'perfect' => 'bg-green-100 text-green-800',
                                            'comfortable' => 'bg-blue-100 text-blue-800',
                                            'tight' => 'bg-yellow-100 text-yellow-800',
                                            'over_capacity' => 'bg-red-100 text-red-800',
                                        ];
                                        $fitLabels = [
                                            'perfect' => 'Perfect Fit',
                                            'comfortable' => 'Comfortable',
                                            'tight' => 'Tight Fit',
                                            'over_capacity' => 'Over Capacity',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $fitColors[$a->fit_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $fitLabels[$a->fit_status] ?? ucfirst($a->fit_status) }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @php
                                            $spare = ($a->room_capacity ?? 0) - ($a->section_strength ?? 0);
                                        @endphp
                                        {{ $spare >= 0 ? '+'.$spare.' spare' : abs($spare).' over' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ ($a->assignment_type ?? 'auto') === 'manual' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($a->assignment_type ?? 'auto') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <form action="{{ route('admin.rooms.adjust') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="section_id" value="{{ $a->section_id }}">
                                        <input type="hidden" name="school_timing_id" value="{{ $session->id }}">
                                        <button class="text-yellow-600 hover:text-yellow-800 bg-yellow-50 rounded-full p-1.5 transition" title="Re-adjust">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.rooms.assignments.remove', $a->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 bg-red-50 rounded-full p-1.5 transition" onclick="return confirm('Remove this assignment?')" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p>No assignments yet.</p>
                                    <form action="{{ route('admin.rooms.auto-assign') }}" method="POST" class="inline ml-2">
                                        @csrf
                                        <input type="hidden" name="timing_id" value="{{ $session->id }}">
                                        <button class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            Auto-Assign Now
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT: Unassigned, manual, free rooms --}}
        <div class="space-y-6">
            {{-- Unassigned sections card --}}
            @if(isset($unassigned) && $unassigned->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-yellow-100">
                <div class="bg-yellow-50 px-5 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-yellow-800">
                        <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Unassigned Sections
                    </h3>
                    <span class="bg-yellow-200 text-yellow-800 text-xs px-2 py-0.5 rounded-full">{{ $unassigned->count() }}</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    @foreach($unassigned as $sec)
                    <div class="p-4 flex justify-between items-center">
                        <div>
                            <div class="font-medium text-gray-900">{{ $sec->section_name }}</div>
                            <div class="text-sm text-gray-500">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                {{ $sec->student_count ?? $sec->student_strength ?? 0 }} students
                            </div>
                        </div>
                        <form action="{{ route('admin.rooms.adjust') }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $sec->id }}">
                            <input type="hidden" name="school_timing_id" value="{{ $session->id }}">
                            <button class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-full p-2 transition" title="Auto-assign">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-green-50 rounded-2xl p-4 text-green-800 text-center">
                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                All sections are assigned!
            </div>
            @endif

            {{-- Manual assignment card --}}
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <svg class="inline w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                    </svg>
                    Manual Override
                </h3>
                <form action="{{ route('admin.rooms.manual-assign') }}" method="POST">
                    @csrf
                    <input type="hidden" name="school_timing_id" value="{{ $session->id }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select name="section_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                            <option value="">-- Select Section --</option>
                            @foreach(\App\Models\ClassSection::with('class.grade')->orderBy('section_name')->get() as $sec)
                                <option value="{{ $sec->id }}">{{ optional(optional($sec->class)->grade)->name ?? 'No Class' }} – {{ $sec->section_name }} ({{ $sec->student_count ?? $sec->student_strength ?? 0 }} students)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                        <select name="room_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                            <option value="">-- Select Room --</option>
                            @foreach(\App\Models\Room::where('is_available', true)->orderBy('capacity')->get() as $rm)
                                <option value="{{ $rm->id }}">{{ $rm->name }} (cap: {{ $rm->capacity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Assign Manually
                    </button>
                </form>
            </div>

            {{-- Free rooms list --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-200 px-5 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">
                        <svg class="inline w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Free Rooms
                    </h3>
                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">{{ $freeRooms->count() }}</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                    @forelse($freeRooms as $fr)
                    <div class="p-3">
                        <div class="font-medium text-gray-900">{{ $fr->name }}</div>
                        <div class="text-xs text-gray-500">
                            <span class="mr-2">📋 {{ $fr->room_number ?? 'N/A' }}</span>
                            <span>👥 {{ $fr->capacity }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-gray-500">No free rooms left.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection