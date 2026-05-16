@extends('layouts.app')

@section('title', 'Certificate Distribution History')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Certificate Distribution History</h1>
        <a href="{{ route('admin.certificates.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Back to Certificates</a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th>Certificate</th>
                    <th>Student</th>
                    <th>Admission No.</th>
                    <th>Issue Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distributions as $dist)
                <tr>
                    <td class="px-6 py-3">{{ $dist->certificateType->title }}</td>
                    <td class="px-6 py-3">{{ $dist->student->full_name }}</td>
                    <td class="px-6 py-3">{{ $dist->student->admission_number }}</td>
                    <td class="px-6 py-3">{{ $dist->issue_date->format('d-m-Y') }}</td>
                    <td class="px-6 py-3">{{ $dist->remarks ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4">No certificates distributed yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $distributions->links() }}
</div>
@endsection