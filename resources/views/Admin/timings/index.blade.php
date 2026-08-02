@extends('layouts.app')

@section('title', 'School Timings')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">⏰ School Timings</h1>
                <p class="text-sm text-gray-500 mt-1">Manage academic sessions and time slots</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.school-timings.create') }}" 
                   class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    ➕ Create Session
                </a>
            </div>
        </div>

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

        @php 
            $activeTiming = \App\Models\SchoolTiming::where('is_active', true)->first(); 
        @endphp
        
        @if($activeTiming)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                <div>
                    <span class="text-green-800 font-medium">✅ Active Session:</span>
                    <span class="text-green-900 font-bold">{{ $activeTiming->session_name }}</span>
                    <span class="text-sm text-green-600 ml-2">
                        ({{ \Carbon\Carbon::parse($activeTiming->school_start)->format('h:i A') }} - 
                        {{ \Carbon\Carbon::parse($activeTiming->school_end)->format('h:i A') }})
                    </span>
                </div>
                <span class="px-3 py-1 bg-green-200 text-green-800 rounded-full text-xs font-bold">ACTIVE</span>
            </div>
        @else
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <span class="text-red-800 font-medium">⚠️ No active session found.</span>
                <span class="text-red-700 text-sm ml-2">Please activate a session or create a new one.</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="font-bold text-gray-800">📋 All Sessions</span>
                <span class="text-xs text-gray-500">{{ $timings->count() }} sessions</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100/70">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Session Name</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Start</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">End</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Slots</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($timings as $timing)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $timing->is_active ? 'bg-green-50/30' : '' }}">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $timing->session_name }}
                                    @if($timing->is_active)
                                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-bold">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($timing->school_start)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($timing->school_end)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $timing->timeSlots->count() }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($timing->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="{{ route('admin.school-timings.show', $timing) }}" 
                                           class="text-blue-600 hover:text-blue-900 px-2 py-1" title="View">👁️</a>
                                        
                                        <a href="{{ route('admin.school-timings.edit', $timing) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 px-2 py-1" title="Edit">✏️</a>
                                        
                                        @if(!$timing->is_active)
                                            <form action="{{ route('admin.school-timings.activate', $timing) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 px-2 py-1 text-xs" title="Activate">✅ Activate</button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.school-timings.regenerate', $timing) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 px-2 py-1 text-xs" title="Regenerate Slots">🔄 Regenerate</button>
                                        </form>
                                        
                                        <form action="{{ route('admin.school-timings.duplicate', $timing) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 px-2 py-1 text-xs" title="Duplicate">📋 Duplicate</button>
                                        </form>
                                        
                                        <form action="{{ route('admin.school-timings.destroy', $timing) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this session? All slots will be lost.');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 text-xs" title="Delete">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="text-4xl mb-2">⏰</div>
                                    <p>No sessions found.</p>
                                    <p class="text-sm text-gray-400 mt-1">Create your first academic session.</p>
                                    <a href="{{ route('admin.school-timings.create') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                        ➕ Create Session
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-xs text-gray-500">Total Sessions</p>
                <p class="text-2xl font-bold text-gray-900">{{ $timings->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-xs text-gray-500">Active Session</p>
                <p class="text-2xl font-bold text-gray-900">{{ $activeTiming ? 1 : 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-xs text-gray-500">Inactive Sessions</p>
                <p class="text-2xl font-bold text-gray-900">{{ $timings->count() - ($activeTiming ? 1 : 0) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-xs text-gray-500">Total Time Slots</p>
                <p class="text-2xl font-bold text-gray-900">
                    @php
                        $totalSlots = 0;
                        foreach($timings as $t) {
                            $totalSlots += $t->timeSlots->count();
                        }
                    @endphp
                    {{ $totalSlots }}
                </p>
            </div>
        </div>

    </div>
</div>
@endsection