@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="max-w-md mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">Record Payment for Installment #{{ $installment->installment_number }}</h2>

        <div class="bg-gray-50 p-3 rounded mb-4">
            <p><strong>Student:</strong> {{ $installment->student->full_name }}</p>
            <p><strong>Fee Type:</strong> {{ $installment->feeType->name }}</p>
            <p><strong>Total Amount:</strong> ₹{{ number_format($installment->amount, 2) }}</p>
            <p><strong>Already Paid:</strong> ₹{{ number_format($installment->paid_amount, 2) }}</p>
            <p><strong>Remaining:</strong> ₹{{ number_format($installment->remaining, 2) }}</p>
        </div>

        <form method="POST" action="{{ route('admin.fee-installments.pay', $installment) }}">
            @csrf

            <div class="space-y-3">
                <div>
                    <label class="block font-medium text-gray-700">Payment Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" min="0.01" max="{{ $installment->remaining }}" value="{{ old('amount', $installment->remaining) }}" class="w-full border rounded px-3 py-2" required>
                    @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required>
                    @error('payment_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Receipt Number</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number') }}" class="w-full border rounded px-3 py-2">
                    @error('receipt_number')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.fee-installments.show', $installment) }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Record Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection