@extends('layouts.app')
@section('title', 'Check In / Check Out')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Employee Attendance Check</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div x-data="{ employee_id: '' }">
            <form method="POST" action="{{ route('admin.attendance.checkin') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-medium">Employee *</label>
                    <select name="employee_id" x-model="employee_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Check In</button>
                    <button type="button" @click="$refs.checkoutForm.submit()" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Check Out</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.attendance.checkout') }}" x-ref="checkoutForm">
                @csrf
                <input type="hidden" name="employee_id" :value="employee_id">
            </form>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>✅ Check In automatically sets status: Present or Late (based on category arrival time).</p>
            <p>✅ Check Out calculates work hours & final status (Half‑day if < 4 hours).</p>
        </div>
    </div>
</div>
@endsection