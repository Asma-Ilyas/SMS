@extends('layouts.app')

@section('title', 'Edit Transfer Certificate')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">✏️ Edit Transfer Certificate</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Certificate #{{ $transferCertificate->certificate_number }}
                    - {{ optional($transferCertificate->student)->first_name }} {{ optional($transferCertificate->student)->last_name }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.transfer-certificates.index') }}" 
                   class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Back
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.transfer-certificates.update', $transferCertificate) }}" class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Student (Read-only) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="font-medium text-gray-900">
                            {{ optional($transferCertificate->student)->first_name }} {{ optional($transferCertificate->student)->last_name }}
                            <span class="text-sm font-normal text-gray-500 ml-2">
                                ({{ optional($transferCertificate->student)->admission_number ?? 'N/A' }})
                            </span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Section: {{ optional(optional($transferCertificate->student)->classSection)->section_name ?? 'N/A' }}
                        </p>
                    </div>
                    <input type="hidden" name="student_id" value="{{ $transferCertificate->student_id }}">
                </div>

                {{-- Certificate Types --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Types <span class="text-red-500">*</span></label>
                    @php
                        $selectedTypes = is_array($transferCertificate->certificate_types) 
                            ? $transferCertificate->certificate_types 
                            : json_decode($transferCertificate->certificate_types, true) ?? [];
                        $certificateTypes = ['Character', 'Academic', 'Sports', 'Conduct', 'Attendance', 'Transfer'];
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($certificateTypes as $type)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="certificate_types[]" value="{{ $type }}" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       {{ in_array($type, old('certificate_types', $selectedTypes)) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $type }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('certificate_types')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Student Status After --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Status After <span class="text-red-500">*</span></label>
                    <select name="student_status_after" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="active" {{ old('student_status_after', $transferCertificate->student_status_after) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('student_status_after', $transferCertificate->student_status_after) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('student_status_after')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Issued Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issued Date <span class="text-red-500">*</span></label>
                    <input type="date" name="issued_date" value="{{ old('issued_date', $transferCertificate->issued_date->format('Y-m-d')) }}" 
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('issued_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks', $transferCertificate->remarks) }}</textarea>
                    @error('remarks')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.transfer-certificates.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    💾 Update Certificate
                </button>
            </div>
        </form>

    </div>
</div>
@endsection