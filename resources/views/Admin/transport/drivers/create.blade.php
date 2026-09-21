@extends('layouts.app')
@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Add Driver</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">CNIC</label><input type="text" name="cnic" value="{{ old('cnic') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">License Number</label><input type="text" name="license_number" value="{{ old('license_number') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">License Expiry</label><input type="date" name="license_expiry" value="{{ old('license_expiry') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" value="{{ old('phone') }}" required class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label><input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Joining Date</label><input type="date" name="joining_date" value="{{ old('joining_date') }}" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Photo</label><input type="file" name="photo" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium hover:file:bg-indigo-100"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">License Document</label><input type="file" name="license_document" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium hover:file:bg-indigo-100"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">CNIC Document</label><input type="file" name="cnic_document" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:text-sm file:font-medium hover:file:bg-indigo-100"></div>
            </div>
            <div class="mt-5"><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea name="address" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('address') }}</textarea></div>
            <div class="mt-5"><label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">{{ old('remarks') }}</textarea></div>
            <div class="mt-5 flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700">Active</label>
            </div>
            <div class="mt-6 flex gap-3">
                <button class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-500/25 hover:bg-indigo-700 transition">Save Driver</button>
                <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
