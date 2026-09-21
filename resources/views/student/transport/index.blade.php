@extends('layouts.app')
@section('title', 'Transport')
@section('content')
@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—';
    $t = fn($x) => $x ? substr($x, 0, 5) : '—';
@endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'Transport', 'subtitle' => 'Your school bus / van details'])

    @if(!$enabled || $assignments->isEmpty())
        @include('student.partials.empty', ['message' => 'You are not assigned to any transport route.'])
    @else
        @foreach($assignments as $a)
            <div class="bg-white rounded-xl border shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">{{ $a->route_name }} <span class="text-xs text-gray-400">{{ $a->route_code }}</span></h2>
                        <p class="text-sm text-gray-500">{{ $a->start_point }} → {{ $a->end_point }}</p>
                    </div>
                    @include('student.partials.badge', ['status' => $a->status])
                </div>
                <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div><dt class="text-xs uppercase text-gray-500">My stop</dt><dd>{{ $a->stop_name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Pickup</dt><dd>{{ $t($a->pickup_time) }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Drop</dt><dd>{{ $t($a->drop_time) }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Period</dt><dd>{{ $fmt($a->start_date) }} → {{ $a->end_date ? $fmt($a->end_date) : 'Ongoing' }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Vehicle</dt><dd>{{ $a->vehicle_number ?? '—' }} @if($a->vehicle_type)({{ ucfirst($a->vehicle_type) }})@endif</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Driver</dt><dd>{{ $a->driver_name ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Driver phone</dt><dd>{{ $a->driver_phone ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase text-gray-500">Fee</dt><dd>{{ $a->fee_name ? $a->fee_name.' – Rs. '.number_format($a->fee_amount,0).' / '.$a->fee_period : '—' }}</dd></div>
                </dl>

                @if(!empty($stops[$a->route_id]) && $stops[$a->route_id]->isNotEmpty())
                    <h3 class="font-semibold text-gray-800 mt-6 mb-2 text-sm">Route stops</h3>
                    <ol class="border-l-2 border-indigo-200 ml-2 space-y-2">
                        @foreach($stops[$a->route_id] as $stop)
                            <li class="pl-4 text-sm {{ $stop->id == $a->route_stop_id ? 'font-semibold text-indigo-700' : 'text-gray-600' }}">
                                {{ $stop->stop_name }} <span class="text-xs text-gray-400">{{ $t($stop->pickup_time) }}</span>
                                @if($stop->id == $a->route_stop_id) <span class="text-xs">(your stop)</span> @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        @endforeach

        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Transport Fee Payments</div>
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
    @endif
</div>
@endsection
