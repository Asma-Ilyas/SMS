@extends('layouts.app')
@section('title', 'Mark Attendance')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-8">
            <h2 class="text-2xl font-bold text-center mb-6">Mark Attendance</h2>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.attendance.checkin') }}" class="mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block font-medium mb-1">Select Employee</label>
                    <select name="employee_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Staff --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Check In</button>
            </form>

            <form method="POST" action="{{ route('admin.attendance.checkout') }}">
                @csrf
                <div class="mb-4">
                    <label class="block font-medium mb-1">Select Employee</label>
                    <select name="employee_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Staff --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">Check Out</button>
            </form>
        </div>
    </div>
</div>
@endsection