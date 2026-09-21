@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Transport &amp; Hostel Alerts</h1>
    <p class="text-sm text-gray-500 mb-6">Expiring documents and overdue fees, computed live from current data.</p>

    @php
        $groups = [
            'driver_licenses' => ['title' => 'Driver License Expiry', 'icon' => '🪪'],
            'vehicle_documents' => ['title' => 'Vehicle Documents (Insurance / Fitness / Registration)', 'icon' => '🚌'],
            'transport_fees_overdue' => ['title' => 'Overdue Transport Fees', 'icon' => '💳'],
            'hostel_fees_overdue' => ['title' => 'Overdue Hostel Fees', 'icon' => '🏠'],
        ];
    @endphp

    @foreach($groups as $key => $meta)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">{{ $meta['icon'] }} {{ $meta['title'] }}</h2>
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ count($alerts[$key]) }}</span>
        </div>
        @if(count($alerts[$key]) === 0)
            <div class="px-6 py-8 text-center text-sm text-gray-400">Nothing here — all clear.</div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($alerts[$key] as $item)
                <a href="{{ $item['url'] }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item['detail'] }}</p>
                    </div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['severity'] === 'expired' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $item['severity'] === 'expired' ? 'Overdue' : 'Soon' }}
                    </span>
                </a>
                @endforeach
            </div>
        @endif
    </div>
    @endforeach
</div>
@endsection
