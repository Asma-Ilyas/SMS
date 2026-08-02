@extends('layouts.app')

@section('title', 'Transfer Certificates')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📜 Transfer Certificates</h1>
                <p class="text-sm text-gray-500 mt-1">Manage student transfer certificates</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.transfer-certificates.create') }}" 
                   class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    ➕ Create Certificate
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
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
        @if(session('info'))
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm">
                ℹ️ {{ session('info') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-sm text-gray-500">Total Certificates</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalCertificates ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-sm text-gray-500">Active Students</p>
                <p class="text-2xl font-bold text-green-600">{{ $activeStudents ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-sm text-gray-500">Inactive Students</p>
                <p class="text-2xl font-bold text-red-600">{{ $inactiveStudents ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <p class="text-sm text-gray-500">This Month</p>
                <p class="text-2xl font-bold text-indigo-600">
                    @php
                        $thisMonth = $certificates->filter(function($cert) {
                            return $cert->issued_date >= now()->startOfMonth();
                        })->count();
                    @endphp
                    {{ $thisMonth }}
                </p>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 mb-6">
            <form method="GET" action="{{ route('admin.transfer-certificates.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, Admission No, Certificate No..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <select name="class_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ optional($class->grade)->name ?? '' }} {{ optional($class->stream)->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Sections</option>
                        @foreach($sections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.transfer-certificates.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        ✕ Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Certificates Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">📋 Certificates List</h3>
                <span class="text-xs text-gray-500">{{ $certificates->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100/70">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Certificate No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Section</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Types</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Issued Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($certificates as $certificate)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $certificate->certificate_number }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-gray-900">
                                        {{ optional($certificate->student)->first_name }} {{ optional($certificate->student)->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ optional($certificate->student)->admission_number ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ optional(optional($certificate->student)->classSection)->section_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $types = is_array($certificate->certificate_types) 
                                            ? $certificate->certificate_types 
                                            : json_decode($certificate->certificate_types, true);
                                    @endphp
                                    @if($types)
                                        @foreach(array_slice($types, 0, 2) as $type)
                                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs mr-1">
                                                {{ $type }}
                                            </span>
                                        @endforeach
                                        @if(count($types) > 2)
                                            <span class="text-xs text-gray-400">+{{ count($types) - 2 }} more</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($certificate->student_status_after == 'active')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($certificate->issued_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="{{ route('admin.transfer-certificates.show', $certificate) }}" 
                                           class="text-blue-600 hover:text-blue-900 px-2 py-1" title="View">👁️</a>
                                        <a href="{{ route('admin.transfer-certificates.edit', $certificate) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 px-2 py-1" title="Edit">✏️</a>
                                        <a href="{{ route('admin.transfer-certificates.download', $certificate->id) }}" 
                                           class="text-green-600 hover:text-green-900 px-2 py-1" title="Download">📥</a>
                                        <form action="{{ route('admin.transfer-certificates.destroy', $certificate) }}" 
                                              method="POST" class="inline-block" 
                                              onsubmit="return confirm('Delete this certificate? This action cannot be undone.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1" title="Delete">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <div class="text-4xl mb-2">📜</div>
                                    <p>No transfer certificates found.</p>
                                    <p class="text-sm text-gray-400 mt-1">Create your first transfer certificate.</p>
                                    <a href="{{ route('admin.transfer-certificates.create') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                        ➕ Create Certificate
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $certificates->links() }}
            </div>
        </div>

    </div>
</div>
@endsection