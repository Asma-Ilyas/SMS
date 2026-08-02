@extends('layouts.app')

@section('title', 'Mark Salary Paid')

@section('content')
<div class="container px-4 py-6 max-w-2xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>✅</span> Mark Salary as Paid
            </h1>
            <p class="text-sm text-slate-500">{{ $salary->staff->full_name }} - {{ $salary->month }}</p>
        </div>
        <a href="{{ route('admin.salaries.show', $salary->id) }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
            ← Back
        </a>
    </div>

    <!-- Salary Info -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400">Employee</p>
                <p class="font-semibold text-slate-800">{{ $salary->staff->full_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Net Salary</p>
                <p class="text-2xl font-bold text-indigo-600">${{ number_format($salary->net_salary, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('admin.salaries.mark-paid.update', $salary->id) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Payment Method *</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                        <option value="bank">🏦 Bank Transfer</option>
                        <option value="cash">💵 Cash</option>
                        <option value="cheque">📝 Cheque</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Transaction Reference</label>
                    <input type="text" name="transaction_ref" value="{{ old('transaction_ref') }}" 
                           placeholder="Enter transaction reference number"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" 
                              placeholder="Any additional remarks...">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.salaries.show', $salary->id) }}" 
                   class="flex-1 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium text-center">
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-sm font-medium">
                    ✅ Confirm Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection