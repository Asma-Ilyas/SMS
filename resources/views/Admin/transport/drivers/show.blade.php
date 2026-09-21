@extends('layouts.app')
@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $driver->name }}</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">CNIC</dt><dd class="text-sm text-gray-900 mt-1">{{ $driver->cnic }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">License #</dt><dd class="text-sm text-gray-900 mt-1">{{ $driver->license_number }} (expires {{ $driver->license_expiry?->format('d M Y') ?? '—' }})</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Phone</dt><dd class="text-sm text-gray-900 mt-1">{{ $driver->phone }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Address</dt><dd class="text-sm text-gray-900 mt-1">{{ $driver->address ?? '—' }}</dd></div>
            <div class="md:col-span-2">
                <dt class="text-xs font-semibold text-gray-500 uppercase">Assigned Vehicles</dt>
                <dd class="mt-1 flex flex-wrap gap-1">
                    @forelse($driver->vehicles as $v)
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $v->vehicle_number }}</span>
                    @empty
                        <span class="text-sm text-gray-400">—</span>
                    @endforelse
                </dd>
            </div>
        </dl>
    </div>
    <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition mt-4">Back</a>
</div>
@endsection
