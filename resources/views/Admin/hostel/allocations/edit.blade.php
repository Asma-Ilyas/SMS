@extends('layouts.app')
@section('content')
<div class="p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Allocation: {{ $allocation->student->first_name }} {{ $allocation->student->last_name }}</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-sm text-gray-500 mb-5">Room and hostel can't be changed here — vacate this allocation and create a new one to move the student to a different room.</p>
        <form action="{{ route('admin.hostel-allocations.update', $allocation) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hostel</label>
                    <input type="text" value="{{ $allocation->hostel->name }}" disabled class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <input type="text" value="{{ $allocation->room->room_number }}" disabled class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Bed Number</label><input type="text" name="bed_number" value="{{ old('bed_number', $allocation->bed_number) }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('remarks', $allocation->remarks) }}</textarea></div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Update Allocation</button>
                <a href="{{ route('admin.hostel-allocations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
