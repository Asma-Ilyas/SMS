@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vehicle Trip Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Manual pickup/drop trip records.</p>
        </div>
        <a href="{{ route('admin.vehicle-trips.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">+ Log Trip</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="vehicle_id" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                <option value="">All Vehicles</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" @selected(request('vehicle_id')==$v->id)>{{ $v->vehicle_number }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            <button class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Route</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Driver</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($trips as $trip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $trip->trip_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $trip->vehicle->vehicle_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $trip->route?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $trip->driver?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ ucfirst($trip->trip_type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $trip->start_time }} — {{ $trip->end_time }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.vehicle-trips.status', $trip) }}" method="POST" class="inline">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-2 py-1 text-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                    @foreach(['scheduled','in_progress','completed','cancelled'] as $s)
                                        <option value="{{ $s }}" @selected($trip->status==$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.vehicle-trips.destroy', $trip) }}" method="POST" onsubmit="return confirm('Delete this trip log?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-10 text-center text-sm text-gray-400">No trip logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $trips->links() }}</div>
</div>
@endsection
