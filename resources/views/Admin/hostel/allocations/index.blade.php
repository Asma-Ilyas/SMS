@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hostel Allocations</h1>
            <p class="text-sm text-gray-500 mt-1">Track which students are in which rooms.</p>
        </div>
        <a href="{{ route('admin.hostel-allocations.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">+ Allocate Student</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    @include('admin.partials.search-bar', ['placeholder' => 'Search student, hostel, room...'])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bed</th>
                        @include('admin.partials.sortable-th', ['field' => 'allocation_date', 'label' => 'Since'])
                        @include('admin.partials.sortable-th', ['field' => 'status', 'label' => 'Status'])
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allocations as $a)
                    <tr class="hover:bg-gray-50" x-data="{ vacateOpen: false }">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $a->student->first_name }} {{ $a->student->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $a->hostel->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $a->room->room_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $a->bed_number ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $a->allocation_date->format('d M Y') }}</td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $a->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($a->status) }}</span></td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.hostel-allocations.show', $a) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                            @if($a->status === 'active')
                            <button @click="vacateOpen = true" class="text-yellow-600 hover:text-yellow-800 font-medium">Vacate</button>

                            <div x-show="vacateOpen" x-cloak
                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div @click.away="vacateOpen = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-left">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vacate Room</h3>
                                    <form action="{{ route('admin.hostel-allocations.vacate', $a) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Vacate Date</label>
                                            <input type="date" name="vacate_date" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                        </div>
                                        <div class="flex gap-3 pt-2">
                                            <button class="flex-1 px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-xl hover:bg-yellow-600 transition">Confirm Vacate</button>
                                            <button type="button" @click="vacateOpen = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                            <form action="{{ route('admin.hostel-allocations.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Remove this allocation record?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                        @if(request('search')) No allocations match "{{ request('search') }}". @else No allocations found. @endif
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $allocations->links() }}</div>
</div>
@endsection
