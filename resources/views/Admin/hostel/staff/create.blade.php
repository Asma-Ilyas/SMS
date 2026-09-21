@extends('layouts.app')
@section('content')
<div class="p-6 max-w-xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Assign Staff to Hostel</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.hostel-staff.store') }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hostel</label>
                    <select name="hostel_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Hostel --</option>
                        @foreach($hostels as $h)
                            <option value="{{ $h->id }}" @selected(old('hostel_id')==$h->id)>{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Staff Member</label>
                    <select name="staff_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="">-- Select Staff --</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}" @selected(old('staff_id')==$s->id)>{{ $s->first_name }} {{ $s->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" required>
                        <option value="warden">Warden</option>
                        <option value="deputy_warden">Deputy Warden</option>
                        <option value="caretaker">Caretaker</option>
                        <option value="security">Security</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Assigned Date</label><input type="date" name="assigned_date" value="{{ old('assigned_date') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Save Assignment</button>
                <a href="{{ route('admin.hostel-staff.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
