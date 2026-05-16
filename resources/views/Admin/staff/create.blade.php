@extends('layouts.app')
@section('title', 'Add Staff')
@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5">
            <h2 class="text-2xl font-bold text-white">Add New Staff</h2>
        </div>
        <form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block font-semibold">First Name *</label><input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">Last Name *</label><input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">CNIC *</label><input type="text" name="cnic" value="{{ old('cnic') }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">Email *</label><input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">Employee ID *</label><input type="text" name="employee_id" value="{{ old('employee_id') }}" class="w-full rounded-lg border-gray-300" required></div>

                <!-- Category Dropdown -->
                <div>
                    <label class="block font-semibold">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div><label class="block font-semibold">Gender</label><select name="gender" class="w-full rounded-lg border-gray-300"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select></div>
                <div><label class="block font-semibold">Date of Birth</label><input type="date" name="date_of_birth" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Phone</label><input type="text" name="phone" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Mobile</label><input type="text" name="mobile" class="w-full rounded-lg border-gray-300"></div>
                <div class="col-span-2"><label class="block font-semibold">Present Address</label><textarea name="present_address" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
                <div><label class="block font-semibold">Designation</label><input type="text" name="designation" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Department</label><input type="text" name="department" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Joining Date</label><input type="date" name="joining_date" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Qualification</label><textarea name="qualification" rows="2" class="w-full rounded-lg border-gray-300"></textarea></div>
                <div><label class="block font-semibold">Basic Salary</label><input type="number" step="0.01" name="basic_salary" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Profile Photo</label><input type="file" name="profile_photo" accept="image/*" class="w-full"></div>
                <div class="flex items-center"><input type="checkbox" name="is_active" value="1" checked> <span class="ml-2">Active</span></div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection