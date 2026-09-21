@extends('layouts.app')
@section('title', 'Hostel')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'Hostel', 'subtitle' => 'Your accommodation details'])

    @if(!$enabled || !$allocation)
        @include('student.partials.empty', ['message' => 'You are not allocated to any hostel.'])
    @else
        <div class="bg-white rounded-xl border shadow-sm p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">{{ $allocation->hostel_name }}</h2>
                    <p class="text-sm text-gray-500">{{ ucfirst($allocation->hostel_type) }} hostel @if($allocation->hostel_address) · {{ $allocation->hostel_address }} @endif</p>
                </div>
                @include('student.partials.badge', ['status' => $allocation->status])
            </div>
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><dt class="text-xs uppercase text-gray-500">Room</dt><dd>{{ $allocation->room_number }} @if($allocation->room_type)({{ $allocation->room_type }})@endif</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Floor</dt><dd>{{ $allocation->floor ?: '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Bed</dt><dd>{{ $allocation->bed_number ?: '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Room capacity</dt><dd>{{ $allocation->room_capacity }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Allocated on</dt><dd>{{ $fmt($allocation->allocation_date) }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Warden</dt><dd>{{ trim($allocation->warden_name) ?: '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Warden phone</dt><dd>{{ $allocation->warden_phone ?: '—' }}</dd></div>
                <div><dt class="text-xs uppercase text-gray-500">Fee</dt><dd>{{ $allocation->fee_name ? $allocation->fee_name.' – Rs. '.number_format($allocation->fee_amount,0).' / '.$allocation->fee_period : '—' }}</dd></div>
            </dl>
            @if($allocation->remarks)<p class="mt-4 text-sm text-gray-600">{{ $allocation->remarks }}</p>@endif
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Hostel Fee Payments</div>
            @if($payments->isEmpty())
                <div class="p-6 text-sm text-gray-500">No fee records yet.</div>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Month</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Paid</th><th class="px-4 py-3">Due</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Receipt</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($payments as $p)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $p->month }}</td>
                                <td class="px-4 py-3">Rs. {{ number_format($p->amount,0) }}</td>
                                <td class="px-4 py-3">Rs. {{ number_format($p->paid_amount,0) }}</td>
                                <td class="px-4 py-3">{{ $fmt($p->due_date) }}</td>
                                <td class="px-4 py-3">@include('student.partials.badge', ['status' => $p->status])</td>
                                <td class="px-4 py-3 text-gray-500">{{ $p->receipt_number ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Hostel Attendance (last 30 records)</div>
            @if($attendance->isEmpty())
                <div class="p-6 text-sm text-gray-500">No hostel attendance recorded.</div>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Check-in</th><th class="px-4 py-3">Check-out</th><th class="px-4 py-3">Remarks</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($attendance as $a)
                            <tr>
                                <td class="px-4 py-3">{{ $fmt($a->attendance_date) }}</td>
                                <td class="px-4 py-3">@include('student.partials.badge', ['status' => $a->status])</td>
                                <td class="px-4 py-3">{{ $a->check_in_time ? substr($a->check_in_time,0,5) : '—' }}</td>
                                <td class="px-4 py-3">{{ $a->check_out_time ? substr($a->check_out_time,0,5) : '—' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $a->remarks }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if($history->isNotEmpty())
            <div class="bg-white rounded-xl border shadow-sm p-5 text-sm">
                <h3 class="font-semibold text-gray-800 mb-2">Previous allocations</h3>
                <ul class="space-y-1 text-gray-600">
                    @foreach($history as $h)
                        <li>{{ $h->hostel_name }} · Room {{ $h->room_number }} · {{ $fmt($h->allocation_date) }} → {{ $fmt($h->vacate_date) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>
@endsection
