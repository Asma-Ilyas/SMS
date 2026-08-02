@extends('layouts.app')

@section('title', 'Time Slots - ' . $schoolTiming->session_name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Time Slots: {{ $schoolTiming->session_name }}</h1>
                <p class="text-gray-500">Drag rows to reorder</p>
            </div>
            <a href="{{ route('admin.slots.create', $schoolTiming) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">+ Add Manual Slot</a>
        </div>

        <!-- Filters -->
        <div class="mb-4 flex gap-2">
            <form method="GET" class="flex flex-1 gap-2">
                <input type="text" name="search" placeholder="Search label or type..." value="{{ request('search') }}" class="rounded-md border-gray-300 shadow-sm flex-1">
                <select name="type" class="rounded-md border-gray-300">
                    <option value="">All Types</option>
                    <option value="period" @selected(request('type')=='period')>Period</option>
                    <option value="break" @selected(request('type')=='break')>Break</option>
                    <option value="activity" @selected(request('type')=='activity')>Activity</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">Filter</button>
                <a href="{{ route('admin.slots.index', $schoolTiming) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md">Reset</a>
            </form>
        </div>

        <!-- Sortable Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200" id="sortable-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Label</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Start</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">End</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Period #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="slot-list">
                    @foreach($slots as $slot)
                    <tr data-id="{{ $slot->id }}" class="cursor-move">
                        <td class="px-6 py-4 text-sm text-gray-500 handle">⋮⋮</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $slot->label }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->start_time }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->end_time }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full
                                @if($slot->type === 'period') bg-blue-100 text-blue-800
                                @elseif($slot->type === 'break') bg-yellow-100 text-yellow-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst($slot->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $slot->period_number ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $slot->is_active ? 'Active' : 'Inactive' }}</td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.slots.edit', [$schoolTiming, $slot]) }}" class="text-indigo-600">Edit</a>
                            <form action="{{ route('admin.slots.toggle', [$schoolTiming, $slot]) }}" method="POST" class="inline-block">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-yellow-600">{{ $slot->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form action="{{ route('admin.slots.destroy', [$schoolTiming, $slot]) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this slot?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $slots->links() }}</div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    const el = document.getElementById('slot-list');
    if (el) {
        new Sortable(el, {
            handle: '.handle',
            onEnd: function() {
                let order = [];
                document.querySelectorAll('#slot-list tr').forEach(row => {
                    order.push(row.dataset.id);
                });
                fetch('{{ route("admin.slots.reorder", $schoolTiming) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ order: order })
                }).then(res => res.json()).then(data => {
                    if (data.status === 'ok') location.reload();
                });
            }
        });
    }
</script>
@endpush
@endsection