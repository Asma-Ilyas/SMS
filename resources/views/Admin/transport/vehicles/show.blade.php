@extends('layouts.app')
@section('content')
<div class="p-6 max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $vehicle->vehicle_number }}</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Type</dt><dd class="text-sm text-gray-900 mt-1">{{ ucfirst($vehicle->type) }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Model</dt><dd class="text-sm text-gray-900 mt-1">{{ $vehicle->model ?? '—' }} ({{ $vehicle->manufacture_year ?? '—' }})</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Seating Capacity</dt><dd class="text-sm text-gray-900 mt-1">{{ $vehicle->seating_capacity }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Driver</dt><dd class="text-sm text-gray-900 mt-1">{{ $vehicle->driver?->name ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Status</dt><dd class="text-sm text-gray-900 mt-1">{{ ucfirst($vehicle->status) }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Insurance Expiry</dt><dd class="text-sm text-gray-900 mt-1">{{ $vehicle->insurance_expiry?->format('d M Y') ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Fitness Expiry</dt><dd class="text-sm text-gray-900 mt-1">{{ $vehicle->fitness_expiry?->format('d M Y') ?? '—' }}</dd></div>
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase">Assigned Routes</dt>
                <dd class="mt-1 flex flex-wrap gap-1">
                    @forelse($vehicle->routes as $r)
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $r->name }}</span>
                    @empty
                        <span class="text-sm text-gray-400">—</span>
                    @endforelse
                </dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Maintenance Log</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Cost</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Next Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vehicle->maintenanceLogs as $log)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $log->maintenance_date->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $log->type }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $log->cost }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $log->next_due_date?->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">No maintenance records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <form action="{{ route('admin.vehicles.maintenance.store', $vehicle) }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
            @csrf
            <input type="date" name="maintenance_date" required class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <input type="text" name="type" placeholder="Type" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <input type="text" name="description" placeholder="Description" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none md:col-span-2">
            <input type="number" step="0.01" name="cost" placeholder="Cost" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <input type="date" name="next_due_date" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <button class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition md:col-span-6">Add Maintenance Record</button>
        </form>
    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Back</a>
        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-800 text-white text-sm font-medium rounded-xl hover:bg-gray-900 transition">Edit</a>
    </div>
</div>
@endsection
