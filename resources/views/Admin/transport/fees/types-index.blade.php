@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transport Fee Types</h1>
            <p class="text-sm text-gray-500 mt-1">Define recurring transport fee amounts.</p>
        </div>
        <a href="{{ route('admin.transport-fee-types.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">+ Add Fee Type</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Route</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($feeTypes as $ft)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ft->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ft->route?->name ?? 'All Routes' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($ft->period) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($ft->amount, 2) }}</td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ft->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $ft->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.transport-fee-types.edit', $ft) }}" class="text-gray-600 hover:text-gray-900 font-medium">Edit</a>
                            <form action="{{ route('admin.transport-fee-types.destroy', $ft) }}" method="POST" class="inline" onsubmit="return confirm('Delete this fee type?')">
                                @csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">No fee types found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $feeTypes->links() }}</div>

    <div class="mt-6">
        <a href="{{ route('admin.transport-fee-payments.index') }}" class="inline-flex items-center px-4 py-2 border border-indigo-200 text-indigo-700 text-sm font-medium rounded-xl hover:bg-indigo-50 transition">View Fee Payments →</a>
    </div>
</div>
@endsection
