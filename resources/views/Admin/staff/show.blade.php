@extends('layouts.app')
@section('title', 'Staff Details')
@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-5 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Staff Details</h2>
            <a href="{{ route('admin.staff.index') }}" class="text-white hover:underline">← Back to List</a>
        </div>
        <div class="p-6">
            <div class="flex items-center space-x-4 mb-6">
                @if($staff->profile_photo)
                    <img src="{{ Storage::url($staff->profile_photo) }}" class="w-24 h-24 rounded-full object-cover border-2 border-blue-500">
                @else
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-2xl font-bold">
                        {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h3 class="text-xl font-bold">{{ $staff->first_name }} {{ $staff->last_name }}</h3>
                    <p class="text-gray-600">{{ $staff->designation ?? 'No designation' }} | {{ $staff->department ?? 'No department' }}</p>
                    <p class="text-sm text-gray-500">Employee ID: {{ $staff->employee_id }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
                <div><span class="font-semibold">CNIC:</span> {{ $staff->cnic }}</div>
                <div><span class="font-semibold">Email:</span> {{ $staff->email }}</div>
                <div><span class="font-semibold">Gender:</span> {{ $staff->gender ?? '—' }}</div>
                <div><span class="font-semibold">Date of Birth:</span> {{ $staff->date_of_birth ? \Carbon\Carbon::parse($staff->date_of_birth)->format('d-m-Y') : '—' }}</div>
                <div><span class="font-semibold">Phone:</span> {{ $staff->phone ?? '—' }}</div>
                <div><span class="font-semibold">Mobile:</span> {{ $staff->mobile ?? '—' }}</div>
                <div class="col-span-2"><span class="font-semibold">Present Address:</span> {{ $staff->present_address ?? '—' }}</div>
                <div><span class="font-semibold">Qualification:</span> {{ $staff->qualification ?? '—' }}</div>
                <div><span class="font-semibold">Joining Date:</span> {{ $staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('d-m-Y') : '—' }}</div>
                <div><span class="font-semibold">Basic Salary:</span> ₹{{ number_format($staff->basic_salary ?? 0, 2) }}</div>
                <div><span class="font-semibold">Category:</span> {{ $staff->category->name ?? '—' }}</div>
                <div><span class="font-semibold">Status:</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $staff->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <a href="{{ route('admin.staff.edit', $staff) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Edit</a>
                <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="inline" onsubmit="return confirm('Delete this staff member?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection