@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Hostel Fee Payments</h1>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <form action="{{ route('admin.hostel-fee-payments.generate') }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Generate for Month</label>
                <input type="month" name="month" required class="rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
            </div>
            <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Generate Monthly Fees</button>
        </form>
        <p class="text-xs text-gray-500 mt-2">Creates a pending fee record for every actively-allocated student who doesn't already have one for that month.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $p)
                    @php $badge = ['paid'=>'bg-green-100 text-green-700','partial'=>'bg-yellow-100 text-yellow-700','pending'=>'bg-gray-100 text-gray-600'][$p->status] ?? 'bg-gray-100 text-gray-600'; @endphp
                    <tr class="hover:bg-gray-50" x-data="{ payOpen: false }">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->allocation->student->first_name }} {{ $p->allocation->student->last_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $p->month }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($p->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($p->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $p->due_date->format('d M Y') }}</td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst($p->status) }}</span></td>
                        <td class="px-6 py-4 text-right">
                            @if($p->status !== 'paid')
                            <button @click="payOpen = true" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Record Payment</button>

                            <div x-show="payOpen" x-cloak
                                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div @click.away="payOpen = false" class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-left">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Record Payment</h3>
                                    <p class="text-sm text-gray-500 mb-4">Remaining: {{ number_format($p->amount - $p->paid_amount, 2) }}</p>
                                    <form action="{{ route('admin.hostel-fee-payments.pay', $p) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
                                            <input type="number" step="0.01" name="paid_amount" max="{{ $p->amount - $p->paid_amount }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                            <input type="date" name="payment_date" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Receipt #</label>
                                            <input type="text" name="receipt_number" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                                        </div>
                                        <div class="flex gap-3 pt-2">
                                            <button class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition">Save Payment</button>
                                            <button type="button" @click="payOpen = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">No fee payment records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
</div>
@endsection
