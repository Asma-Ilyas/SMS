@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
        <div class="p-8 text-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Today's Attendance</h2>
                <p class="text-gray-500">{{ now()->format('l, d F Y') }}</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col space-y-4">
                <!-- Check-in button -->
                <form method="POST" action="{{ route('attendance.checkin') }}">
                    @csrf
                    <button type="submit"
                        @if(isset($todayAttendance) && $todayAttendance->check_in && !$todayAttendance->check_out) disabled @endif
                        class="w-full py-3 bg-green-600 text-white rounded-lg text-xl font-semibold
                               hover:bg-green-700 transition
                               disabled:opacity-50 disabled:cursor-not-allowed">
                        Check In
                    </button>
                </form>

                <!-- Check-out button -->
                <form method="POST" action="{{ route('attendance.checkout') }}">
                    @csrf
                    <button type="submit"
                        @if(!isset($todayAttendance) || !$todayAttendance->check_in || $todayAttendance->check_out) disabled @endif
                        class="w-full py-3 bg-red-600 text-white rounded-lg text-xl font-semibold
                               hover:bg-red-700 transition
                               disabled:opacity-50 disabled:cursor-not-allowed">
                        Check Out
                    </button>
                </form>
            </div>

            @if(isset($todayAttendance) && $todayAttendance->check_in)
                <div class="mt-6 p-4 bg-gray-100 rounded-lg text-left">
                    <p class="text-sm text-gray-600"><strong>Check In:</strong> {{ $todayAttendance->check_in }}</p>
                    @if($todayAttendance->check_out)
                        <p class="text-sm text-gray-600"><strong>Check Out:</strong> {{ $todayAttendance->check_out }}</p>
                        <p class="text-sm text-gray-600"><strong>Total Hours:</strong> {{ $todayAttendance->total_hours ?? 'Calculating...' }}</p>
                    @endif
                </div>
            @endif

            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> Check in when you start work and check out when you finish. Your working hours will be automatically recorded.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection