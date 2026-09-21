@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hostels</h1>
            <p class="text-sm text-gray-500 mt-1">Manage hostel buildings.</p>
        </div>
        <a href="{{ route('admin.hostels.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">+ Add Hostel</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    @include('admin.partials.search-bar', ['placeholder' => 'Search hostel name, code...'])

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @include('admin.partials.sortable-th', ['field' => 'code', 'label' => 'Code'])
                        @include('admin.partials.sortable-th', ['field' => 'name', 'label' => 'Name'])
                        @include('admin.partials.sortable-th', ['field' => 'type', 'label' => 'Type'])
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Warden</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rooms</th>
                        @include('admin.partials.sortable-th', ['field' => 'is_active', 'label' => 'Status'])
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($hostels as $hostel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $hostel->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $hostel->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($hostel->type) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $hostel->warden ? $hostel->warden->first_name.' '.$hostel->warden->last_name : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $hostel->rooms_count }}</td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hostel->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $hostel->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.hostels.show', $hostel) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                            <a href="{{ route('admin.hostels.edit', $hostel) }}" class="text-gray-600 hover:text-gray-900 font-medium">Edit</a>
                            <form action="{{ route('admin.hostels.destroy', $hostel) }}" method="POST" class="inline" onsubmit="return confirm('Delete this hostel?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                        @if(request('search')) No hostels match "{{ request('search') }}". @else No hostels found. @endif
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $hostels->links() }}</div>
</div>
@endsection
