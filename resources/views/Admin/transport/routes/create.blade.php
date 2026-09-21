@extends('layouts.app')
@section('content')
<div class="p-6 max-w-4xl" x-data="{ stops: [{ name: '', pickup: '', drop: '' }] }">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Add Transport Route</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.transport-routes.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Route Code</label><input type="text" name="code" value="{{ old('code') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Point</label><input type="text" name="start_point" value="{{ old('start_point') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">End Point</label><input type="text" name="end_point" value="{{ old('end_point') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Distance (km)</label><input type="number" step="0.01" name="distance_km" value="{{ old('distance_km') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Estimated Time (min)</label><input type="number" name="estimated_time_minutes" value="{{ old('estimated_time_minutes') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign Vehicle</label>
                    <select name="vehicle_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- None --</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id')==$vehicle->id)>{{ $vehicle->vehicle_number }} ({{ ucfirst($vehicle->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign Driver</label>
                    <select name="driver_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- None --</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" @selected(old('driver_id')==$driver->id)>{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-5"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('description') }}</textarea></div>

            <hr class="my-6 border-gray-200">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-base font-semibold text-gray-900">Stops (in pickup order)</h2>
                <button type="button" @click="stops.push({ name: '', pickup: '', drop: '' })"
                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition">+ Add Stop</button>
            </div>

            <template x-for="(stop, i) in stops" :key="i">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 mb-2 items-end">
                    <div class="md:col-span-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Stop Name</label>
                        <input type="text" :name="'stops[' + i + '][stop_name]'" x-model="stop.name" required class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pickup Time</label>
                        <input type="time" :name="'stops[' + i + '][pickup_time]'" x-model="stop.pickup" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Drop Time</label>
                        <input type="time" :name="'stops[' + i + '][drop_time]'" x-model="stop.drop" class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="md:col-span-1">
                        <button type="button" @click="stops.splice(i, 1)" class="w-full px-2 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">✕</button>
                    </div>
                </div>
            </template>

            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Save Route</button>
                <a href="{{ route('admin.transport-routes.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
