@extends('layouts.app')
@section('title', 'Payslip '.$salary->month)
@section('content')
@php
    $money = fn($v) => 'Rs. '.number_format((float) $v, 0);
    $earn = ['Basic salary' => $salary->basic_salary, 'Allowances' => $salary->allowances, 'Attendance bonus' => $salary->attendance_bonus, 'Overtime pay' => $salary->overtime_pay, 'Bonus' => $salary->bonus, 'Commission' => $salary->commission];
    $ded  = ['Deductions' => $salary->deductions, 'Penalties' => $salary->penalties, 'Leave deductions' => $salary->leave_deductions, 'Tax' => $salary->tax, 'Provident fund (employee)' => $salary->pf_employee];
@endphp
<div class="space-y-6 max-w-3xl">
    <div class="print:hidden">@include('teacher.partials.header', ['title' => 'Payslip — '.$salary->month, 'subtitle' => $t->first_name.' '.$t->last_name])</div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <div class="text-center border-b pb-4 mb-4">
            <h2 class="text-xl font-bold text-gray-800">Salary Slip</h2>
            <p class="text-sm text-gray-500">{{ $salary->month }}</p>
        </div>
        <dl class="grid grid-cols-2 gap-3 text-sm mb-6">
            <div><dt class="text-xs uppercase text-gray-500">Employee</dt><dd class="font-medium">{{ $t->first_name }} {{ $t->last_name }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Employee ID</dt><dd>{{ $t->employee_id }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Designation</dt><dd>{{ $t->designation ?: '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Attendance</dt><dd>{{ $salary->days_present ?? '—' }} present / {{ $salary->total_working_days ?? '—' }} days · {{ $salary->days_absent ?? 0 }} absent · {{ $salary->days_late ?? 0 }} late</dd></div>
        </dl>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-green-700 mb-2">Earnings</h3>
                @foreach($earn as $l => $v)<div class="flex justify-between text-sm py-1 border-b"><span>{{ $l }}</span><span>{{ $money($v) }}</span></div>@endforeach
            </div>
            <div>
                <h3 class="font-semibold text-red-700 mb-2">Deductions</h3>
                @foreach($ded as $l => $v)<div class="flex justify-between text-sm py-1 border-b"><span>{{ $l }}</span><span>{{ $money($v) }}</span></div>@endforeach
            </div>
        </div>
        <div class="mt-6 flex justify-between items-center bg-indigo-50 rounded-lg p-4">
            <span class="font-semibold text-gray-800">Net salary</span><span class="text-2xl font-bold text-indigo-700">{{ $money($salary->net_salary) }}</span>
        </div>
        <dl class="grid grid-cols-2 gap-3 text-sm mt-4">
            <div><dt class="text-xs uppercase text-gray-500">Status</dt><dd>@include('teacher.partials.badge', ['status' => $salary->payment_status])</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Method</dt><dd>{{ ucfirst($salary->payment_method) }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Payment date</dt><dd>{{ $salary->payment_date ? \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') : '—' }}</dd></div>
            <div><dt class="text-xs uppercase text-gray-500">Reference</dt><dd>{{ $salary->transaction_ref ?: '—' }}</dd></div>
        </dl>
        @if($salary->remarks)<p class="mt-4 text-sm text-gray-600"><strong>Remarks:</strong> {{ $salary->remarks }}</p>@endif
    </div>
    <div class="print:hidden flex gap-4"><a href="{{ route('teacher.salary.index') }}" class="text-sm text-indigo-600 hover:underline">← Back</a><button onclick="window.print()" class="text-sm text-gray-600 hover:underline">🖨 Print</button></div>
</div>
@endsection
