@extends('layouts.app')

@section('title', 'Salary Preview - ' . $staff->full_name)

@section('content')
<div class="container px-4 py-6 max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <span>💰</span> Salary Preview
            </h1>
            <p class="text-sm text-slate-500">{{ $staff->full_name }} - {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.salaries.index', ['month' => $month]) }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Employee Info -->
        <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-slate-100">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-slate-400">Employee</p>
                    <p class="font-semibold text-slate-800">{{ $staff->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Designation</p>
                    <p class="font-semibold text-slate-800">{{ $staff->designation ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Basic Salary</p>
                    <p class="font-semibold text-slate-800">${{ number_format($staff->basic_salary, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Net Salary</p>
                    <p class="font-semibold text-indigo-600">${{ number_format($calculated['net_salary'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">📅 Attendance Summary</h3>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                <div class="bg-slate-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-slate-400">Working Days</p>
                    <p class="text-xl font-bold text-slate-800">{{ $calculated['total_working_days'] }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-emerald-400">Present</p>
                    <p class="text-xl font-bold text-emerald-600">{{ $calculated['days_present'] }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-red-400">Absent</p>
                    <p class="text-xl font-bold text-red-500">{{ $calculated['days_absent'] }}</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-amber-400">Late</p>
                    <p class="text-xl font-bold text-amber-500">{{ $calculated['days_late'] }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-purple-400">Leave</p>
                    <p class="text-xl font-bold text-purple-600">{{ $calculated['days_leave'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-blue-400">Attendance %</p>
                    <p class="text-xl font-bold text-blue-600">{{ $calculated['total_working_days'] > 0 ? round(($calculated['days_present'] / $calculated['total_working_days']) * 100, 1) : 0 }}%</p>
                </div>
            </div>
        </div>

        <!-- Salary Breakdown -->
        <div class="p-6">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">💰 Salary Breakdown</h3>
            <div class="space-y-2">
                <!-- Earnings -->
                <div class="bg-emerald-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">Earnings</p>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Basic Salary</span>
                            <span class="font-semibold text-slate-800">${{ number_format($calculated['basic_salary'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Allowances</span>
                            <span class="font-semibold text-slate-800">${{ number_format($calculated['allowances'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Attendance Bonus</span>
                            <span class="font-semibold text-slate-800">${{ number_format($calculated['attendance_bonus'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Overtime Pay</span>
                            <span class="font-semibold text-slate-800">${{ number_format($calculated['overtime_pay'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold border-t border-emerald-200 pt-2 mt-2">
                            <span>Total Earnings</span>
                            <span class="text-emerald-600">${{ number_format($calculated['basic_salary'] + $calculated['allowances'] + $calculated['attendance_bonus'] + $calculated['overtime_pay'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Deductions</p>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Absent Deduction ({{ $calculated['days_absent'] }} days)</span>
                            <span class="font-semibold text-red-500">${{ number_format($calculated['deductions'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Late Deduction ({{ $calculated['days_late'] }} days)</span>
                            <span class="font-semibold text-red-500">${{ number_format($calculated['late_deductions'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Leave Deduction</span>
                            <span class="font-semibold text-red-500">${{ number_format($calculated['leave_deductions'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Penalties</span>
                            <span class="font-semibold text-red-500">${{ number_format($calculated['penalties'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Tax</span>
                            <span class="font-semibold text-red-500">${{ number_format($calculated['tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">PF (Employee)</span>
                            <span class="font-semibold text-red-500">${{ number_format($calculated['pf_employee'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold border-t border-red-200 pt-2 mt-2">
                            <span>Total Deductions</span>
                            <span class="text-red-600">${{ number_format($calculated['total_deductions'] + $calculated['tax'] + $calculated['pf_employee'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Net Salary -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border-2 border-indigo-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold text-indigo-600">💰 Net Salary</p>
                            <p class="text-xs text-slate-400">After all earnings and deductions</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-indigo-600">${{ number_format($calculated['net_salary'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
            <form method="POST" action="{{ route('admin.salaries.calculate', $staff->id) }}" class="flex-1">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm font-medium">
                    💾 Save Salary Record
                </button>
            </form>
            <a href="{{ route('admin.salaries.index', ['month' => $month]) }}" class="flex-1 px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition text-sm font-medium text-center">
                Cancel
            </a>
        </div>
    </div>
</div>
@endsection