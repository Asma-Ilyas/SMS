{{-- resources/views/Admin/fees/submission-edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Fee Submission')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">✏️ Edit Fee Submission</h1>
                    <p class="text-gray-500 mt-1">Update fee submission details</p>
                </div>
                <a href="{{ route('admin.fee-submissions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form action="{{ route('admin.fee-submissions.update', $submission->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student *</label>
                        <select name="student_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                            @foreach($students ?? [] as $student)
                                <option value="{{ $student->id }}" {{ $submission->student_id == $student->id ? 'selected' : '' }}>
                                    {{ $student->full_name }} ({{ $student->admission_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type *</label>
                        <select name="fee_type_id" required class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                            @foreach($feeTypes ?? [] as $feeType)
                                <option value="{{ $feeType->id }}" {{ $submission->fee_type_id == $feeType->id ? 'selected' : '' }}>
                                    {{ $feeType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fee_type_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="amount" step="0.01" value="{{ old('amount', $submission->amount) }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500" 
                               placeholder="0.00" required>
                        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                        <select name="period" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                            <option value="monthly" {{ old('period', $submission->period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('period', $submission->period) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="annually" {{ old('period', $submission->period) == 'annually' ? 'selected' : '' }}>Annually</option>
                            <option value="one_time" {{ old('period', $submission->period) == 'one_time' ? 'selected' : '' }}>One Time</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Submission Date *</label>
                        <input type="date" name="submission_date" value="{{ old('submission_date', $submission->submission_date ? $submission->submission_date->format('Y-m-d') : date('Y-m-d')) }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500" required>
                        @error('submission_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
                        <input type="text" name="receipt_number" value="{{ old('receipt_number', $submission->receipt_number) }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500" 
                               placeholder="RCP-000001">
                        @error('receipt_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="paid" {{ old('status', $submission->status) == 'paid' ? 'selected' : '' }}>✅ Paid</option>
                        <option value="pending" {{ old('status', $submission->status) == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="partial" {{ old('status', $submission->status) == 'partial' ? 'selected' : '' }}>🔶 Partial</option>
                        <option value="overdue" {{ old('status', $submission->status) == 'overdue' ? 'selected' : '' }}>🔴 Overdue</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">{{ old('remarks', $submission->remarks) }}</textarea>
                    @error('remarks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                        💾 Update Submission
                    </button>
                    <a href="{{ route('admin.fee-submissions.index') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection