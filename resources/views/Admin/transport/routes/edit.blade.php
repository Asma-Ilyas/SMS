@extends('layouts.app')
@section('content')
<div class="p-6 max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Route: {{ $route->name }}</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.transport-routes.update', $route) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label><input type="text" name="name" value="{{ old('name', $route->name) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Route Code</label><input type="text" name="code" value="{{ old('code', $route->code) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Point</label><input type="text" name="start_point" value="{{ old('start_point', $route->start_point) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">End Point</label><input type="text" name="end_point" value="{{ old('end_point', $route->end_point) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Distance (km)</label><input type="number" step="0.01" name="distance_km" value="{{ old('distance_km', $route->distance_km) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Estimated Time (min)</label><input type="number" name="estimated_time_minutes" value="{{ old('estimated_time_minutes', $route->estimated_time_minutes) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="1" @selected($route->is_active)>Active</option>
                        <option value="0" @selected(!$route->is_active)>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign Vehicle</label>
                    <select name="vehicle_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- None --</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $route->vehicle_id)==$vehicle->id)>{{ $vehicle->vehicle_number }} ({{ ucfirst($vehicle->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign Driver</label>
                    <select name="driver_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- None --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" @selected(old('driver_id', $route->driver_id)==$driver->id)>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-5"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('description', $route->description) }}</textarea></div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Update Route</button>
                <a href="{{ route('admin.transport-routes.show', $route) }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Stops</h2>
        <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Pickup</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Drop</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($route->stops as $stop)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $stop->stop_order }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $stop->stop_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $stop->pickup_time }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $stop->drop_time }}</td>
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('admin.transport-routes.stops.remove', [$route, $stop->id]) }}" method="POST" onsubmit="return confirm('Remove this stop?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-400">No stops yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <form action="{{ route('admin.transport-routes.stops.add', $route) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-2">
            @csrf
            <input type="text" name="stop_name" placeholder="Stop name" required class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <input type="time" name="pickup_time" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <input type="time" name="drop_time" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <button class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Add Stop</button>
        </form>
    </div>
</div>
@endsection
