@extends('layouts.app')
@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Assign Student to Route</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.student-transports.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <select name="student_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected(old('student_id')==$student->id)>{{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route</label>
                    <select name="route_id" id="routeSelect" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Route --</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" @selected(old('route_id')==$route->id)>{{ $route->name }} ({{ $route->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pickup/Drop Stop</label>
                    <select name="route_stop_id" id="stopSelect" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">-- Select Stop --</option>
                    </select>
                </div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label><input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('remarks') }}</textarea></div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Save Assignment</button>
                <a href="{{ route('admin.student-transports.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const routesData = @json($routes->keyBy('id'));
    const routeSelect = document.getElementById('routeSelect');
    const stopSelect = document.getElementById('stopSelect');

    function populateStops() {
        const routeId = routeSelect.value;
        stopSelect.innerHTML = '<option value="">-- Select Stop --</option>';
        if (routeId && routesData[routeId]) {
            routesData[routeId].stops.forEach(function (stop) {
                const opt = document.createElement('option');
                opt.value = stop.id;
                opt.textContent = stop.stop_name;
                stopSelect.appendChild(opt);
            });
        }
    }

    routeSelect.addEventListener('change', populateStops);
    populateStops();
</script>
@endsection
