@extends('layouts.app')
@section('title', 'Salary Slip')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Salary Slip</h2>
                    <p class="text-gray-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month)->format('F Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold">{{ $salary->employee->name }}</p>
                    <p class="text-sm text-gray-500">{{ $salary->employee->position }}</p>
                    <p class="text-sm text-gray-500">{{ $salary->employee->employee_id }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div class="flex justify-between py-2 border-b">
                    <span>Basic Salary</span>
                    <span>₹{{ number_format($salary->basic_salary, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-green-600">
                    <span>Allowances</span>
                    <span>+ ₹{{ number_format($salary->allowances, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-red-600">
                    <span>Deductions</span>
                    <span>- ₹{{ number_format($salary->deductions, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-red-600">
                    <span>Tax</span>
                    <span>- ₹{{ number_format($salary->tax, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b text-red-600">
                    <span>PF (Employee)</span>
                    <span>- ₹{{ number_format($salary->pf_employee, 2) }}</span>
                </div>
                <div class="flex justify-between py-3 text-lg font-bold bg-gray-50 px-3 rounded">
                    <span>Net Salary</span>
                    <span class="text-green-700">₹{{ number_format($salary->net_salary, 2) }}</span>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t text-sm text-gray-500">
                <p>Payment Date: {{ $salary->payment_date->format('d-m-Y') }}</p>
                <p>Payment Method: {{ ucfirst($salary->payment_method) }}</p>
                @if($salary->remarks)<p>Remarks: {{ $salary->remarks }}</p>@endif
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('salaries.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Back</a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Print Slip</button>
            </div>
        </div>
    </div>
</div>
@endsection