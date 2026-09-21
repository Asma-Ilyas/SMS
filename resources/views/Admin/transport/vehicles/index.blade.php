@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vehicles</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your fleet of buses and vans.</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}"
           class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">
            + Add Vehicle
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>
    @endif

    @include('admin.partials.search-bar', ['placeholder' => 'Search vehicle #, model, driver...'])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @include('admin.partials.sortable-th', ['field' => 'vehicle_number', 'label' => 'Vehicle #'])
                        @include('admin.partials.sortable-th', ['field' => 'type', 'label' => 'Type'])
                        @include('admin.partials.sortable-th', ['field' => 'seating_capacity', 'label' => 'Capacity'])
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Driver</th>
                        @include('admin.partials.sortable-th', ['field' => 'status', 'label' => 'Status'])
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vehicles as $vehicle)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $vehicle->vehicle_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($vehicle->type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $vehicle->seating_capacity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $vehicle->driver?->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = ['active' => 'bg-green-100 text-green-700', 'maintenance' => 'bg-yellow-100 text-yellow-700', 'inactive' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$vehicle->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($vehicle->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-gray-600 hover:text-gray-900 font-medium">Edit</a>
                            <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Delete this vehicle?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                        @if(request('search')) No vehicles match "{{ request('search') }}". @else No vehicles found. @endif
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $vehicles->links() }}</div>
</div>
@endsection
