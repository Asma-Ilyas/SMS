@extends('layouts.app')

@section('title', 'Fee Installments')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Fee Installments</h1>
        <a href="{{ route('admin.fee-installments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Create Installment Plan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inst. #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount (₹)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid (₹)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($installments as $inst)
                <tr>
                    <td class="px-6 py-3">{{ $inst->student->full_name ?? 'N/A' }}<br><small class="text-gray-500">{{ $inst->student->admission_number ?? '' }}</small></td>
                    <td class="px-6 py-3">{{ $inst->feeType->name ?? 'N/A' }}</td>
                    <td class="px-6 py-3">{{ $inst->installment_number }}</td>
                    <td class="px-6 py-3">{{ number_format($inst->amount, 2) }}</td>
                    <td class="px-6 py-3">{{ number_format($inst->paid_amount, 2) }}</td>
                    <td class="px-6 py-3 {{ $inst->remaining > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($inst->remaining, 2) }}</td>
                    <td class="px-6 py-3">{{ $inst->due_date->format('d-m-Y') }}</td>
                    <td class="px-6 py-3">
                        @if($inst->status == 'paid')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Paid</span>
                        @elseif($inst->status == 'partial')
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 space-x-2">
                        <a href="{{ route('admin.fee-installments.show', $inst) }}" class="text-blue-600 hover:underline">View</a>
                        @if($inst->status != 'paid')
                            <a href="{{ route('admin.fee-installments.pay-form', $inst) }}" class="text-green-600 hover:underline">Pay</a>
                        @endif
                        <form action="{{ route('admin.fee-installments.destroy', $inst) }}" method="POST" class="inline" onsubmit="return confirm('Delete this installment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">No installments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $installments->links() }}
    </div>
</div>
@endsection