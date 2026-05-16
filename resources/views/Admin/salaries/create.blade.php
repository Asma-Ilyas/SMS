@extends('layouts.app')
@section('title', 'Add Salary')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Add Single Salary Record</h1>
            </div>
            <form method="POST" action="{{ route('admin.salaries.store') }}" class="p-6 space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Month <span class="text-red-500">*</span></label>
                    <input type="month" name="month" value="{{ old('month') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('month')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                        <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Allowances</label>
                        <input type="number" step="0.01" name="allowances" value="{{ old('allowances', 0) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deductions</label>
                        <input type="number" step="0.01" name="deductions" value="{{ old('deductions', 0) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="bank">Bank Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea name="remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('remarks') }}</textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.salaries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection