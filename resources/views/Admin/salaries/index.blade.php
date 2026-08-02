@extends('layouts.app')

@section('title', 'Salary Management')

@section('content')
<div class="container px-4 py-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <span>💰</span> Salary Management
                </h1>
                <p class="text-indigo-100 text-sm mt-1">Manage employee salaries and payroll</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="GET" action="{{ route('admin.salaries.index') }}" class="flex gap-2">
                    <select name="month" class="px-3 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-white border border-white/10 text-sm">
                        @foreach($months as $key => $label)
                            <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }} class="text-slate-800">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-lg transition text-sm">Filter</button>
                </form>
                <form method="POST" action="{{ route('admin.salaries.generate-payroll') }}" class="inline">
                    @csrf
                    <input type="hidden" name="month" value="{{ $selectedMonth }}">
                    <button type="submit" onclick="return confirm('Generate payroll for {{ $selectedMonth }}?')" 
                            class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition text-sm font-medium">
                        ⚡ Generate Payroll
                    </button>
                </form>
                <a href="{{ route('admin.salaries.export', ['month' => $selectedMonth]) }}" 
                   class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition text-sm font-medium">
                    📥 Export CSV
                </a>
                <a href="{{ route('admin.salaries.templates') }}" 
                   class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition text-sm font-medium">
                    📋 Templates
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-xl">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="text-xl">❌</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:shadow-md transition">
            <p class="text-xs text-slate-400">Total Employees</p>
            <p class="text-2xl font-bold text-slate-800">{{ $staff->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:shadow-md transition">
            <p class="text-xs text-slate-400">Total Salary</p>
            <p class="text-2xl font-bold text-indigo-600">${{ number_format($salaries->sum('net_salary'), 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:shadow-md transition">
            <p class="text-xs text-slate-400">Paid</p>
            <p class="text-2xl font-bold text-emerald-600">${{ number_format($salaries->where('payment_status', 'paid')->sum('net_salary'), 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 hover:shadow-md transition">
            <p class="text-xs text-slate-400">Pending</p>
            <p class="text-2xl font-bold text-amber-500">${{ number_format($salaries->where('payment_status', 'pending')->sum('net_salary'), 2) }}</p>
        </div>
    </div>

    <!-- Salary Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Employee</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Designation</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Basic Salary</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Allowances</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Deductions</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Tax</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Net Salary</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($staff as $employee)
                        @php
                            $salary = $salaries->get($employee->id);
                            $statusBg = $salary && $salary->payment_status == 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
                            $statusText = $salary && $salary->payment_status == 'paid' ? '✅ Paid' : '⏳ Pending';
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $employee->full_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $employee->employee_id }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $employee->designation ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-slate-700">${{ number_format($salary ? $salary->basic_salary : $employee->basic_salary, 2) }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">${{ number_format($salary ? $salary->allowances : 0, 2) }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">${{ number_format($salary ? $salary->deductions + $salary->tax : 0, 2) }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">${{ number_format($salary ? $salary->tax : 0, 2) }}</td>
                            <td class="px-4 py-3 text-center font-bold text-indigo-600">${{ number_format($salary ? $salary->net_salary : 0, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($salary && $salary->payment_status == 'pending')
                                        <a href="{{ route('admin.salaries.mark-paid', $salary->id) }}" 
                                           class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg transition text-xs font-medium">
                                            Mark Paid
                                        </a>
                                    @endif
                                    @if($salary)
                                        <a href="{{ route('admin.salaries.show', $salary->id) }}" 
                                           class="px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition text-xs font-medium">
                                            View
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                <span class="text-2xl block mb-2">📋</span>
                                No salary records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection