@extends('layouts.app')
@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Assignment: {{ $assignment->student->first_name }} {{ $assignment->student->last_name }}</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.student-transports.update', $assignment) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route</label>
                    <select name="route_id" id="routeSelect" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" @selected(old('route_id', $assignment->route_id)==$route->id)>{{ $route->name }} ({{ $route->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pickup/Drop Stop</label>
                    <select name="route_stop_id" id="stopSelect" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- Select Stop --</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label><input type="date" name="start_date" value="{{ old('start_date', $assignment->start_date?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">End Date</label><input type="date" name="end_date" value="{{ old('end_date', $assignment->end_date?->format('Y-m-d')) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="active" @selected($assignment->status=='active')>Active</option>
                        <option value="inactive" @selected($assignment->status=='inactive')>Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('remarks', $assignment->remarks) }}</textarea></div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Update Assignment</button>
                <a href="{{ route('admin.student-transports.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const routesData = @json($routes->keyBy('id'));
    const routeSelect = document.getElementById('routeSelect');
    const stopSelect = document.getElementById('stopSelect');
    const currentStopId = "{{ old('route_stop_id', $assignment->route_stop_id) }}";

    function populateStops() {
        const routeId = routeSelect.value;
        stopSelect.innerHTML = '<option value="">-- Select Stop --</option>';
        if (routeId && routesData[routeId]) {
            routesData[routeId].stops.forEach(function (stop) {
                const opt = document.createElement('option');
                opt.value = stop.id;
                opt.textContent = stop.stop_name;
                if (String(stop.id) === String(currentStopId)) opt.selected = true;
                stopSelect.appendChild(opt);
            });
        }
    }

    routeSelect.addEventListener('change', populateStops);
    populateStops();
</script>
@endsection
