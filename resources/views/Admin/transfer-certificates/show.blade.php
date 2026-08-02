@extends('layouts.app')

@section('title', 'View Transfer Certificate')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">👁️ Transfer Certificate Details</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Certificate #{{ $transferCertificate->certificate_number }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.transfer-certificates.index') }}" 
                   class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Back
                </a>
                <a href="{{ route('admin.transfer-certificates.edit', $transferCertificate) }}" 
                   class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    ✏️ Edit
                </a>
                <a href="{{ route('admin.transfer-certificates.download', $transferCertificate->id) }}" 
                   class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    📥 Download PDF
                </a>
            </div>
        </div>

        {{-- Certificate Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-800">📄 Certificate Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Certificate Number</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $transferCertificate->certificate_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Issued Date</p>
                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($transferCertificate->issued_date)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Student</p>
                    <p class="font-medium text-gray-900">
                        {{ optional($transferCertificate->student)->first_name }} {{ optional($transferCertificate->student)->last_name }}
                    </p>
                    <p class="text-sm text-gray-600">Admission: {{ optional($transferCertificate->student)->admission_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Section</p>
                    <p class="font-medium text-gray-900">
                        {{ optional(optional($transferCertificate->student)->classSection)->section_name ?? 'N/A' }}
                        ({{ optional(optional(optional($transferCertificate->student)->classSection)->class)->grade->name ?? 'N/A' }})
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Certificate Types</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @php
                            $types = is_array($transferCertificate->certificate_types) 
                                ? $transferCertificate->certificate_types 
                                : json_decode($transferCertificate->certificate_types, true) ?? [];
                        @endphp
                        @foreach($types as $type)
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                {{ $type }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Student Status</p>
                    <p>
                        @if($transferCertificate->student_status_after == 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Active</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Inactive</span>
                        @endif
                    </p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Remarks</p>
                    <p class="font-medium text-gray-900">{{ $transferCertificate->remarks ?? 'No remarks' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Created At</p>
                    <p class="text-sm text-gray-600">{{ $transferCertificate->created_at->format('d M Y h:i A') }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection