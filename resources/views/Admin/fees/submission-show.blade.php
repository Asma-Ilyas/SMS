{{-- resources/views/Admin/fees/submission-show.blade.php --}}

@extends('layouts.app')

@section('title', 'Fee Submission Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">👤 Fee Submission Details</h1>
                    <p class="text-gray-500 mt-1">View complete fee submission information</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.fee-submissions.edit', $submission->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        ✏️ Edit
                    </a>
                    <a href="{{ route('admin.fee-submissions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        ← Back
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Info -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                    <h2 class="text-lg font-bold">👤 Student Information</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Full Name</label>
                            <p class="text-gray-800 font-medium">{{ $submission->student->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Admission Number</label>
                            <p class="text-gray-800 font-medium">{{ $submission->student->admission_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Class</label>
                            <p class="text-gray-800 font-medium">{{ $submission->student->classSection->full_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Contact</label>
                            <p class="text-gray-800 font-medium">{{ $submission->student->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Details -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-teal-500 text-white">
                    <h2 class="text-lg font-bold">💰 Fee Details</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Fee Type</label>
                            <p class="text-gray-800 font-medium">{{ $submission->feeType->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Period</label>
                            <p class="text-gray-800 font-medium capitalize">{{ $submission->period ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Amount</label>
                            <p class="text-2xl font-bold text-indigo-600">₹{{ number_format($submission->amount ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Status</label>
                            @php
                                $status = $submission->status ?? 'pending';
                                $statusColors = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'partial' => 'bg-blue-100 text-blue-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 text-sm rounded-full {{ $color }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Submission Date</label>
                            <p class="text-gray-800 font-medium">{{ $submission->submission_date ? $submission->submission_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-medium">Receipt Number</label>
                            <p class="text-gray-800 font-medium">{{ $submission->receipt_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($submission->remarks)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <label class="text-xs text-gray-400 font-medium">Remarks</label>
                        <p class="text-gray-800 mt-1">{{ $submission->remarks }}</p>
                    </div>
                    @endif

                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm">
                        <span class="text-gray-500">Created: {{ $submission->created_at ? $submission->created_at->format('d M Y H:i') : 'N/A' }}</span>
                        <span class="text-gray-400 ml-4">Updated: {{ $submission->updated_at ? $submission->updated_at->format('d M Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection