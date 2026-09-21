@extends('layouts.app')
@section('content')
<div class="p-6" x-data="{ createOpen: false }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hostel Room Types</h1>
            <p class="text-sm text-gray-500 mt-1">Default capacities used when creating new rooms.</p>
        </div>
        <button @click="createOpen = true" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">+ Add Room Type</button>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Default Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rooms Using This Type</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roomTypes as $rt)
                    <tr class="hover:bg-gray-50" x-data="{ editOpen: false }">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $rt->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $rt->default_capacity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $rt->rooms_count }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <button @click="editOpen = true" class="text-gray-600 hover:text-gray-900 font-medium">Edit</button>
                            <form action="{{ route('admin.hostel-room-types.destroy', $rt) }}" method="POST" class="inline" onsubmit="return confirm('Delete this room type?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>

                            <div x-show="editOpen" x-cloak
                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div @click.away="editOpen = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-left">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Room Type</h3>
                                    <form action="{{ route('admin.hostel-room-types.update', $rt) }}" method="POST" class="space-y-3">
                                        @csrf @method('PUT')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                            <input type="text" name="name" value="{{ $rt->name }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Default Capacity</label>
                                            <input type="number" name="default_capacity" value="{{ $rt->default_capacity }}" min="1" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ $rt->description }}</textarea>
                                        </div>
                                        <div class="flex gap-3 pt-2">
                                            <button class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Update</button>
                                            <button type="button" @click="editOpen = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">No room types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $roomTypes->links() }}</div>

    <div x-show="createOpen" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div @click.away="createOpen = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Room Type</h3>
            <form action="{{ route('admin.hostel-room-types.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Capacity</label>
                    <input type="number" name="default_capacity" min="1" value="1" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Save</button>
                    <button type="button" @click="createOpen = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
