@extends('layouts.app')

@section('title', 'Salary Details')

@section('content')
<div class="container px-4 py-6 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>💰</span> Salary Details
            </h1>
            <p class="text-sm text-slate-500">{{ $salary->staff->full_name }} - {{ $salary->month }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.salaries.index', ['month' => $salary->month]) }}" 
               class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                ← Back
            </a>
            @if($salary->payment_status == 'pending')
                <a href="{{ route('admin.salaries.mark-paid', $salary->id) }}" 
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-sm font-medium">
                    ✅ Mark as Paid
                </a>
            @endif
        </div>
    </div>

    <!-- Employee Info -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-slate-400">Employee</p>
                <p class="font-semibold text-slate-800">{{ $salary->staff->full_name }}</p>
                <p class="text-xs text-slate-400">{{ $salary->staff->employee_id }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Designation</p>
                <p class="font-semibold text-slate-800">{{ $salary->staff->designation ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Month</p>
                <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($salary->month . '-01')->format('F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Status</p>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $salary->payment_status == 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $salary->payment_status == 'paid' ? '✅ Paid' : '⏳ Pending' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Salary Breakdown -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">Salary Breakdown</h3>
        </div>
        <div class="divide-y divide-slate-100">
            <!-- Earnings -->
            <div class="p-6">
                <h4 class="text-sm font-semibold text-emerald-600 mb-4">📈 Earnings</h4>
                <div class="space-y-2">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Basic Salary</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->basic_salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Allowances</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->allowances, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Attendance Bonus</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->attendance_bonus ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Overtime Pay</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->overtime_pay ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Bonus</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->bonus ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Commission</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->commission ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 font-bold text-emerald-600 border-t-2 border-emerald-100 pt-3">
                        <span>Total Earnings</span>
                        <span>${{ number_format($salary->basic_salary + $salary->allowances + ($salary->attendance_bonus ?? 0) + ($salary->overtime_pay ?? 0) + ($salary->bonus ?? 0) + ($salary->commission ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="p-6">
                <h4 class="text-sm font-semibold text-red-600 mb-4">📉 Deductions</h4>
                <div class="space-y-2">
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">PF (Employee)</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->pf_employee, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">PF (Employer)</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->pf_employer, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Tax</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Penalties</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->penalties ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Leave Deductions</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->leave_deductions ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-600">Other Deductions</span>
                        <span class="font-semibold text-slate-800">${{ number_format($salary->deductions, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 font-bold text-red-600 border-t-2 border-red-100 pt-3">
                        <span>Total Deductions</span>
                        <span>${{ number_format($salary->pf_employee + $salary->pf_employer + $salary->tax + ($salary->penalties ?? 0) + ($salary->leave_deductions ?? 0) + $salary->deductions, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Net Salary -->
            <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-sm font-semibold text-indigo-600">💰 Net Salary</h4>
                        <p class="text-xs text-slate-400">After all earnings and deductions</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-indigo-600">${{ number_format($salary->net_salary, 2) }}</p>
                        <p class="text-xs text-slate-400">{{ $salary->payment_status == 'paid' ? 'Paid on ' . $salary->payment_date->format('d M Y') : 'Pending' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    @if($salary->payment_status == 'paid')
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mt-6">
        <h4 class="text-sm font-semibold text-slate-700 mb-4">💳 Payment Details</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-slate-400">Payment Date</p>
                <p class="font-semibold text-slate-800">{{ $salary->payment_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Payment Method</p>
                <p class="font-semibold text-slate-800">{{ ucfirst($salary->payment_method) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Transaction Ref</p>
                <p class="font-semibold text-slate-800">{{ $salary->transaction_ref ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Approved By</p>
                <p class="font-semibold text-slate-800">{{ $salary->approvedBy->full_name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Attendance Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mt-6">
        <h4 class="text-sm font-semibold text-slate-700 mb-4">📅 Attendance Summary</h4>
        <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
            <div class="text-center p-3 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-400">Working Days</p>
                <p class="text-xl font-bold text-slate-800">{{ $salary->total_working_days }}</p>
            </div>
            <div class="text-center p-3 bg-emerald-50 rounded-lg">
                <p class="text-xs text-emerald-400">Present</p>
                <p class="text-xl font-bold text-emerald-600">{{ $salary->days_present }}</p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-lg">
                <p class="text-xs text-red-400">Absent</p>
                <p class="text-xl font-bold text-red-500">{{ $salary->days_absent }}</p>
            </div>
            <div class="text-center p-3 bg-amber-50 rounded-lg">
                <p class="text-xs text-amber-400">Late</p>
                <p class="text-xl font-bold text-amber-500">{{ $salary->days_late }}</p>
            </div>
            <div class="text-center p-3 bg-indigo-50 rounded-lg">
                <p class="text-xs text-indigo-400">Attendance %</p>
                <p class="text-xl font-bold text-indigo-600">{{ $salary->total_working_days > 0 ? round(($salary->days_present / $salary->total_working_days) * 100, 1) : 0 }}%</p>
            </div>
        </div>
    </div>
</div>
@endsection