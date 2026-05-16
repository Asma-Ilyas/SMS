@extends('layouts.app')
@section('title', 'Edit Salary')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Edit Salary Record</h1>
            </div>
            <form method="POST" action="{{ route('salaries.update', $salary) }}" class="p-6 space-y-6">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="employee_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $salary->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Month</label>
                    <input type="month" name="month" value="{{ $salary->month }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label>Basic Salary</label><input type="number" step="0.01" name="basic_salary" value="{{ $salary->basic_salary }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                    <div><label>Allowances</label><input type="number" step="0.01" name="allowances" value="{{ $salary->allowances }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                    <div><label>Deductions</label><input type="number" step="0.01" name="deductions" value="{{ $salary->deductions }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                </div>
                <div>
                    <label>Payment Date</label>
                    <input type="date" name="payment_date" value="{{ $salary->payment_date->format('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label>Payment Method</label>
                    <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="bank" {{ $salary->payment_method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ $salary->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                </div>
                <div>
                    <label>Remarks</label>
                    <textarea name="remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $salary->remarks }}</textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('salaries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Update Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection