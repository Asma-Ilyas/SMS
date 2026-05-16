{{-- resources/views/admin/fees/submission-edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Fee Payment')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Fee Payment</h2>

        <form method="POST" action="{{ route('admin.fee-submissions.update', $submission->id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block font-medium">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" class="w-full border rounded px-3 py-2">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id', $submission->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium">Fee Type <span class="text-red-500">*</span></label>
                    <select name="fee_submission_type_id" class="w-full border rounded px-3 py-2">
                        <option value="">Select Fee Type</option>
                        @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}" {{ old('fee_submission_type_id', $submission->fee_submission_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} - {{ ucfirst($type->period) }} - ₹{{ number_format($type->amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('fee_submission_type_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium">Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $submission->amount) }}" class="w-full border rounded px-3 py-2">
                    @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium">Submission Date <span class="text-red-500">*</span></label>
                    <input type="date" name="submission_date" value="{{ old('submission_date', $submission->submission_date->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                    @error('submission_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium">Receipt Number</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number', $submission->receipt_number) }}" class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full border rounded px-3 py-2">{{ old('remarks', $submission->remarks) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.fee-submissions.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection