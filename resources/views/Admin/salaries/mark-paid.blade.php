@extends('layouts.app')
@section('title', 'Mark Salary as Paid')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h1 class="text-2xl font-bold">Mark Salary as Paid</h1>
            <p class="text-sm text-gray-600 mt-1">
                Employee: <strong>{{ $salary->staff->first_name }} {{ $salary->staff->last_name }}</strong><br>
                Month: <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month)->format('F Y') }}</strong><br>
                Net Salary: <strong>₹{{ number_format($salary->net_salary, 2) }}</strong>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.salaries.mark-paid.update', $salary) }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" 
                           class="w-full border rounded px-3 py-2" required>
                    @error('payment_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium text-sm">Payment Method *</label>
                    <select name="payment_method" class="w-full border rounded px-3 py-2" required>
                        <option value="bank">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                    </select>
                    @error('payment_method')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div>
                <label class="block font-medium text-sm">Transaction Reference (optional)</label>
                <input type="text" name="transaction_ref" value="{{ old('transaction_ref', $salary->transaction_ref) }}" 
                       class="w-full border rounded px-3 py-2" placeholder="e.g., UTR number, cheque number">
                @error('transaction_ref')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium text-sm">Remarks (optional)</label>
                <textarea name="remarks" rows="2" class="w-full border rounded px-3 py-2">{{ old('remarks') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.salaries.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Confirm & Mark as Paid
                </button>
            </div>
        </form>
    </div>
</div>
@endsection