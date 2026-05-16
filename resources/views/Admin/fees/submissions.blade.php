@extends('layouts.app')
@section('title', 'Fee Submissions')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Fee Submissions</h1>
        <a href="{{ route('admin.fee-submissions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Record Payment</a>
    </div>
    @if(session('success'))<div class="bg-green-100 p-3 mb-4 rounded">{{ session('success') }}</div>@endif
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th>Student (Adm No)</th>
                    <th>Class / Section</th>
                    <th>Fee Type</th>
                    <th>Period</th>
                    <th>Amount (₹)</th>
                    <th>Submission Date</th>
                    <th>Receipt No</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                <tr>
                    <td>{{ $sub->student->full_name }} ({{ $sub->student->admission_number }})</td>
                    <td>{{ $sub->student->classSection ?? '-' }}</td>
                    <td>{{ $sub->feeType->name }}</td>
                    <td>{{ ucfirst($sub->period) }}</td>
                    <td>₹{{ number_format($sub->amount,2) }}</td>
                    <td>{{ $sub->submission_date->format('d-m-Y') }}</td>
                    <td>{{ $sub->receipt_number ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.fee-submissions.show', $sub) }}" class="text-blue-600">View</a>
                        <a href="{{ route('admin.fee-submissions.edit', $sub) }}" class="text-yellow-600 ml-2">Edit</a>
                        <form action="{{ route('admin.fee-submissions.destroy', $sub) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No fee submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $submissions->links() }}
</div>
@endsection