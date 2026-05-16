@extends('layouts.app')
@section('title', 'Fee Payment Details')
@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Payment Details</h2>
        <p><strong>Student:</strong> {{ $feeSubmission->student->full_name }} ({{ $feeSubmission->student->admission_number }})</p>
        <p><strong>Class/Section:</strong> {{ $feeSubmission->student->classSection }}</p>
        <p><strong>Fee Type:</strong> {{ $feeSubmission->feeType->name }}</p>
        <p><strong>Period:</strong> {{ ucfirst($feeSubmission->period) }}</p>
        <p><strong>Amount:</strong> ₹{{ number_format($feeSubmission->amount,2) }}</p>
        <p><strong>Submission Date:</strong> {{ $feeSubmission->submission_date->format('d-m-Y') }}</p>
        <p><strong>Receipt #:</strong> {{ $feeSubmission->receipt_number ?? '-' }}</p>
        <p><strong>Remarks:</strong> {{ $feeSubmission->remarks ?? '-' }}</p>
        <div class="mt-4">
            <a href="{{ route('admin.fee-submissions.edit', $feeSubmission) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
            <a href="{{ route('admin.fee-submissions.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
        </div>
    </div>
</div>
@endsection