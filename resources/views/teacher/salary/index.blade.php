@extends('layouts.app')
@section('title', 'My Salary')
@section('content')
@php $money = fn($v) => 'Rs. '.number_format((float) $v, 0); @endphp
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Salary', 'subtitle' => 'Salary slips for ' . $year])

    <form method="GET" class="flex items-end gap-3">
        <div><label class="text-xs text-gray-500">Year</label>
            <select name="year" onchange="this.form.submit()" class="block mt-1 rounded-lg border-gray-300 text-sm">
                @forelse($years as $y)<option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>@empty<option>{{ $year }}</option>@endforelse
            </select></div>
    </form>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([['Gross earned', $totals['gross'], 'text-gray-800'], ['Deductions', $totals['ded'], 'text-red-600'], ['Net salary', $totals['net'], 'text-indigo-600'], ['Paid', $totals['paid'], 'text-green-600']] as [$l, $v, $c])
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">{{ $l }}</div><div class="text-2xl font-bold {{ $c }}">{{ $money($v) }}</div></div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        @if($salaries->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">No salary records for {{ $year }}.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Month</th><th class="px-4 py-3">Basic</th><th class="px-4 py-3">Allowances</th><th class="px-4 py-3">Deductions</th><th class="px-4 py-3">Net</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Paid on</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($salaries as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">{{ $s->month }}</td>
                            <td class="px-4 py-2">{{ $money($s->basic_salary) }}</td>
                            <td class="px-4 py-2">{{ $money($s->allowances + $s->bonus + $s->overtime_pay + $s->attendance_bonus + $s->commission) }}</td>
                            <td class="px-4 py-2 text-red-600">{{ $money($s->deductions + $s->penalties + $s->leave_deductions + $s->tax + $s->pf_employee) }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $money($s->net_salary) }}</td>
                            <td class="px-4 py-2">@include('teacher.partials.badge', ['status' => $s->payment_status])</td>
                            <td class="px-4 py-2">{{ $s->payment_date ? \Carbon\Carbon::parse($s->payment_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-2 text-right"><a href="{{ route('teacher.salary.show', $s->id) }}" class="text-indigo-600 hover:underline">Payslip</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
