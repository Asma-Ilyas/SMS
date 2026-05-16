@extends('layouts.app')
@section('title', 'My Leave Requests')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">My Leave Requests</h1>
        <button onclick="openLeaveModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Request Leave</button>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th>Start Date</th><th>End Date</th><th>Type</th><th>Reason</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($leaves as $leave)
                <tr>
                    <td>{{ $leave->start_date->format('d-m-Y') }}</td>
                    <td>{{ $leave->end_date->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($leave->type) }}</td>
                    <td>{{ $leave->reason }}</td>
                    <td><span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($leave->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal for leave request --}}
<div id="leaveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between mb-4"><h3 class="text-xl font-bold">Request Leave</h3><button onclick="closeLeaveModal()" class="text-gray-400">&times;</button></div>
        <form method="POST" action="{{ route('leaves.request') }}">
            @csrf
            <div class="mb-3"><label>Start Date</label><input type="date" name="start_date" class="w-full border rounded-md" required></div>
            <div class="mb-3"><label>End Date</label><input type="date" name="end_date" class="w-full border rounded-md" required></div>
            <div class="mb-3"><label>Type</label><select name="type" class="w-full border rounded-md"><option value="sick">Sick</option><option value="casual">Casual</option><option value="annual">Annual</option></select></div>
            <div class="mb-3"><label>Reason</label><textarea name="reason" rows="3" class="w-full border rounded-md" required></textarea></div>
            <div class="flex justify-end space-x-2"><button type="button" onclick="closeLeaveModal()" class="px-4 py-2 border rounded-md">Cancel</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Submit</button></div>
        </form>
    </div>
</div>
<script>
function openLeaveModal() { document.getElementById('leaveModal').classList.remove('hidden'); }
function closeLeaveModal() { document.getElementById('leaveModal').classList.add('hidden'); }
</script>
@endsection