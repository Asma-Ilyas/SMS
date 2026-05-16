@extends('layouts.app')
@section('title', 'Edit Staff')
@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-5">
            <h2 class="text-2xl font-bold text-white">Edit Staff</h2>
        </div>
        <form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data" class="px-6 py-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block font-semibold">First Name *</label><input type="text" name="first_name" value="{{ old('first_name', $staff->first_name) }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">Last Name *</label><input type="text" name="last_name" value="{{ old('last_name', $staff->last_name) }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">CNIC *</label><input type="text" name="cnic" value="{{ old('cnic', $staff->cnic) }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">Email *</label><input type="email" name="email" value="{{ old('email', $staff->email) }}" class="w-full rounded-lg border-gray-300" required></div>
                <div><label class="block font-semibold">Employee ID *</label><input type="text" name="employee_id" value="{{ old('employee_id', $staff->employee_id) }}" class="w-full rounded-lg border-gray-300" required></div>

                <!-- Category Dropdown -->
                <div>
                    <label class="block font-semibold">Category</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $staff->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div><label class="block font-semibold">Gender</label><select name="gender" class="w-full rounded-lg border-gray-300"><option value="">Select</option><option {{ $staff->gender == 'Male' ? 'selected' : '' }}>Male</option><option {{ $staff->gender == 'Female' ? 'selected' : '' }}>Female</option><option {{ $staff->gender == 'Other' ? 'selected' : '' }}>Other</option></select></div>
                <div><label class="block font-semibold">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth ? \Carbon\Carbon::parse($staff->date_of_birth)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Phone</label><input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Mobile</label><input type="text" name="mobile" value="{{ old('mobile', $staff->mobile) }}" class="w-full rounded-lg border-gray-300"></div>
                <div class="col-span-2"><label class="block font-semibold">Present Address</label><textarea name="present_address" rows="2" class="w-full rounded-lg border-gray-300">{{ old('present_address', $staff->present_address) }}</textarea></div>
                <div><label class="block font-semibold">Designation</label><input type="text" name="designation" value="{{ old('designation', $staff->designation) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Department</label><input type="text" name="department" value="{{ old('department', $staff->department) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Joining Date</label><input type="date" name="joining_date" value="{{ old('joining_date', $staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Qualification</label><textarea name="qualification" rows="2" class="w-full rounded-lg border-gray-300">{{ old('qualification', $staff->qualification) }}</textarea></div>
                <div><label class="block font-semibold">Basic Salary</label><input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $staff->basic_salary) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label class="block font-semibold">Profile Photo</label>
                    @if($staff->profile_photo)<img src="{{ Storage::url($staff->profile_photo) }}" class="w-16 h-16 rounded-full mb-2">@endif
                    <input type="file" name="profile_photo" accept="image/*" class="w-full">
                </div>
                <div class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ $staff->is_active ? 'checked' : '' }}> <span class="ml-2">Active</span></div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection