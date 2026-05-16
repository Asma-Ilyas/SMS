@extends('layouts.app')
@section('title', 'Leave Requests')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Leave Requests</h1>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $leave->employee->name }}</td>
                        <td class="px-6 py-4">{{ $leave->start_date->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">{{ $leave->end_date->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">{{ ucfirst($leave->type) }}</td>
                        <td class="px-6 py-4">{{ $leave->reason }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($leave->status == 'approved') bg-green-100 text-green-800
                                @elseif($leave->status == 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($leave->status == 'pending')
                            <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button class="text-green-600 hover:text-green-900">Approve</button>
                            </form>
                            <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button class="text-red-600 hover:text-red-900">Reject</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection