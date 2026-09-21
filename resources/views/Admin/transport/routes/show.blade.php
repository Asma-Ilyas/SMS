@extends('layouts.app')
@section('content')
<div class="p-6 max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $route->name }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $route->code }}</p>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">From — To</dt><dd class="text-sm text-gray-900 mt-1">{{ $route->start_point ?? '—' }} → {{ $route->end_point ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Vehicle</dt><dd class="text-sm text-gray-900 mt-1">{{ $route->vehicle?->vehicle_number ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Driver</dt><dd class="text-sm text-gray-900 mt-1">{{ $route->driver?->name ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Distance</dt><dd class="text-sm text-gray-900 mt-1">{{ $route->distance_km ?? '—' }} km, ~{{ $route->estimated_time_minutes ?? '—' }} min</dd></div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Stops</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Stop</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Pickup</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Drop</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($route->stops as $stop)
                    <tr><td class="px-4 py-2 text-sm text-gray-700">{{ $stop->stop_order }}</td><td class="px-4 py-2 text-sm text-gray-700">{{ $stop->stop_name }}</td><td class="px-4 py-2 text-sm text-gray-700">{{ $stop->pickup_time }}</td><td class="px-4 py-2 text-sm text-gray-700">{{ $stop->drop_time }}</td></tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">No stops.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Students on this Route ({{ $route->studentTransports->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Stop</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($route->studentTransports as $st)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $st->student->first_name }} {{ $st->student->last_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $st->stop?->stop_name ?? '—' }}</td>
                        <td class="px-4 py-2"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $st->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($st->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-sm text-gray-400">No students assigned yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.transport-routes.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Back</a>
        <a href="{{ route('admin.transport-routes.edit', $route) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-xl hover:bg-gray-900 transition">Edit</a>
    </div>
</div>
@endsection
