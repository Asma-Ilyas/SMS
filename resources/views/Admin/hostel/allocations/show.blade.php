@extends('layouts.app')
@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $allocation->student->first_name }} {{ $allocation->student->last_name }} — Hostel</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Hostel</dt><dd class="text-sm text-gray-900 mt-1">{{ $allocation->hostel->name }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Room</dt><dd class="text-sm text-gray-900 mt-1">{{ $allocation->room->room_number }} {{ $allocation->bed_number ? '(Bed '.$allocation->bed_number.')' : '' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Fee Type</dt><dd class="text-sm text-gray-900 mt-1">{{ $allocation->feeType?->name ?? '—' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Period</dt><dd class="text-sm text-gray-900 mt-1">{{ $allocation->allocation_date->format('d M Y') }} — {{ $allocation->vacate_date?->format('d M Y') ?? 'ongoing' }}</dd></div>
            <div><dt class="text-xs font-semibold text-gray-500 uppercase">Status</dt><dd class="mt-1"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $allocation->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($allocation->status) }}</span></dd></div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Fee Payment History</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Month</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Paid</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allocation->feePayments as $p)
                    @php $badge = ['paid'=>'bg-green-100 text-green-700','partial'=>'bg-yellow-100 text-yellow-700','pending'=>'bg-gray-100 text-gray-600'][$p->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $p->month }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $p->amount }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $p->paid_amount }}</td>
                        <td class="px-4 py-2"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">No fee records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.hostel-allocations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition mt-4">Back</a>
</div>
@endsection
