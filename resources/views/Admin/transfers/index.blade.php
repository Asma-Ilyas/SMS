@extends('layouts.app')

@section('title', 'Student Transfers')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Student Transfers</h1>
        <a href="{{ route('admin.transfers.incoming.form') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Transfer In (New Admission)</a>
    </div>

    <div x-data="{ tab: 'outgoing' }">
        <div class="border-b border-gray-200 mb-4">
            <nav class="-mb-px flex space-x-8">
                <button @click="tab = 'outgoing'" :class="{ 'border-blue-500 text-blue-600': tab === 'outgoing' }" class="py-2 px-1 border-b-2 font-medium text-sm">
                    Students Transferred to Other School
                </button>
                <button @click="tab = 'incoming'" :class="{ 'border-blue-500 text-blue-600': tab === 'incoming' }" class="py-2 px-1 border-b-2 font-medium text-sm">
                    Students Transferred to this School
                </button>
            </nav>
        </div>

        {{-- Outgoing Transfers Table --}}
        <div x-show="tab === 'outgoing'">
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr><th>Student Name</th><th>Admission No.</th><th>Class</th><th>Transferred To</th><th>Date</th><th>Reason</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($outgoingTransfers as $transfer)
                        <tr>
                            <td>{{ $transfer->student->full_name }}</td>
                            <td>{{ $transfer->student->admission_number }}</td>
                            <td>{{ $transfer->student->class->full_name ?? '-' }}</td>
                            <td>{{ $transfer->to_school }}</td>
                            <td>{{ $transfer->transfer_date->format('d-m-Y') }}</td>
                            <td>{{ Str::limit($transfer->reason, 50) }}</td>
                            <td><a href="{{ route('admin.students.show', $transfer->student) }}" class="text-blue-600">View Student</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4">No outgoing transfers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $outgoingTransfers->links() }}
        </div>

        {{-- Incoming Transfers Table --}}
        <div x-show="tab === 'incoming'">
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr><th>Student Name</th><th>Admission No.</th><th>Class</th><th>From School</th><th>Transfer Date</th><th>Reason</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($incomingTransfers as $transfer)
                        <tr>
                            <td>{{ $transfer->student->full_name }}</td>
                            <td>{{ $transfer->student->admission_number }}</td>
                            <td>{{ $transfer->student->class->full_name ?? '-' }}</td>
                            <td>{{ $transfer->from_school }}</td>
                            <td>{{ $transfer->transfer_date->format('d-m-Y') }}</td>
                            <td>{{ Str::limit($transfer->reason, 50) }}</td>
                            <td><a href="{{ route('admin.students.show', $transfer->student) }}" class="text-blue-600">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4">No incoming transfers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $incomingTransfers->links() }}
        </div>
    </div>
</div>
@endsection