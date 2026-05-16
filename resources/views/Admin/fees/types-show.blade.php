{{-- resources/views/admin/fees/types-show.blade.php --}}
@extends('layouts.app')

@section('title', 'Fee Type Details')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">Fee Type Details</h2>
        <div class="border-t border-b py-4 space-y-2">
            <p><strong>Name:</strong> {{ $feeType->name }}</p>
            <p><strong>Period:</strong> {{ ucfirst(str_replace('_', ' ', $feeType->period)) }}</p>
            <p><strong>Amount:</strong> ₹{{ number_format($feeType->amount, 2) }}</p>
            <p><strong>Description:</strong> {{ $feeType->description ?? 'N/A' }}</p>
            <p><strong>Status:</strong> 
                @if($feeType->is_active)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                @else
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                @endif
            </p>
            <p><strong>Created:</strong> {{ $feeType->created_at->format('d-m-Y H:i') }}</p>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('admin.fee-types.edit', $feeType) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
            <a href="{{ route('admin.fee-types.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back to List</a>
        </div>
    </div>
</div>
@endsection