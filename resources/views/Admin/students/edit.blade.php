{{-- resources/views/admin/students/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Student Information</h2>

        <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- PERSONAL DETAILS --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">👤 Personal Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" class="w-full border rounded px-3 py-2">
                        @error('first_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" class="w-full border rounded px-3 py-2">
                        @error('last_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" class="w-full border rounded px-3 py-2">
                            <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                        @error('date_of_birth')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">DOB in words</label>
                        <input type="text" name="dob_in_words" value="{{ old('dob_in_words', $student->dob_in_words) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Religion</label>
                        <input type="text" name="religion" value="{{ old('religion', $student->religion) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Caste / Sub Caste</label>
                        <input type="text" name="caste_subcaste" value="{{ old('caste_subcaste', $student->caste_subcaste) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Blood Group</label>
                        <select name="blood_group" class="w-full border rounded px-3 py-2">
                            <option value="">Select</option>
                            <option value="A+" {{ old('blood_group', $student->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group', $student->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group', $student->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group', $student->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="O+" {{ old('blood_group', $student->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group', $student->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                            <option value="AB+" {{ old('blood_group', $student->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group', $student->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                        </select>
                    </div>

                    <div class="col-span-full">
                        <label class="block font-medium">Address</label>
                        <textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address', $student->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $student->email) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">City</label>
                        <input type="text" name="city" value="{{ old('city', $student->city) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">State</label>
                        <input type="text" name="state" value="{{ old('state', $student->state) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Country</label>
                        <input type="text" name="country" value="{{ old('country', $student->country) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="col-span-full">
                        <label class="block font-medium">Extra Note / Detail</label>
                        <textarea name="extra_note" rows="2" class="w-full border rounded px-3 py-2">{{ old('extra_note', $student->extra_note) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium">Mother Tongue</label>
                        <input type="text" name="mother_tongue" value="{{ old('mother_tongue', $student->mother_tongue) }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Birth Place</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </fieldset>

            {{-- PREVIOUS SCHOOL DETAILS --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🏫 Previous School Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">School Name</label>
                        <input type="text" name="previous_school_name" value="{{ old('previous_school_name', $student->previous_school_name) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">School Address</label>
                        <input type="text" name="previous_school_address" value="{{ old('previous_school_address', $student->previous_school_address) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Class</label>
                        <input type="text" name="previous_class" value="{{ old('previous_class', $student->previous_class) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Passout Year</label>
                        <input type="text" name="passout_year" value="{{ old('passout_year', $student->passout_year) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Category</label>
                        <select name="previous_category" class="w-full border rounded px-3 py-2">
                            <option value="">Select Category</option>
                            <option value="General" {{ old('previous_category', $student->previous_category) == 'General' ? 'selected' : '' }}>General</option>
                            <option value="OBC" {{ old('previous_category', $student->previous_category) == 'OBC' ? 'selected' : '' }}>OBC</option>
                            <option value="SC" {{ old('previous_category', $student->previous_category) == 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="ST" {{ old('previous_category', $student->previous_category) == 'ST' ? 'selected' : '' }}>ST</option>
                            <option value="EWS" {{ old('previous_category', $student->previous_category) == 'EWS' ? 'selected' : '' }}>EWS</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            {{-- ADMISSION DETAILS --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">📅 Admission Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium">Admission Date <span class="text-red-500">*</span></label>
                        <input type="date" name="admission_date" value="{{ old('admission_date', $student->admission_date->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                        @error('admission_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Student Type <span class="text-red-500">*</span></label>
                        <input type="text" name="student_type" value="{{ old('student_type', $student->student_type) }}" class="w-full border rounded px-3 py-2">
                        @error('student_type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                  <div>
    <label class="block font-medium">Class <span class="text-red-500">*</span></label>
    <select name="class_id" class="w-full border rounded px-3 py-2">
        <option value="">Select Class</option>
        @foreach($classes as $class)
            <option value="{{ $class->id }}" {{ old('class_id', $student->class_id ?? '') == $class->id ? 'selected' : '' }}>
                {{ $class->full_name }}
            </option>
        @endforeach
    </select>
    @error('class_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
</div>
                    <div>
                        <label class="block font-medium">Section <span class="text-red-500">*</span></label>
                        <input type="text" name="section" value="{{ old('section', $student->section) }}" class="w-full border rounded px-3 py-2">
                        @error('section')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Admission Number <span class="text-red-500">*</span></label>
                        <input type="text" name="admission_number" value="{{ old('admission_number', $student->admission_number) }}" class="w-full border rounded px-3 py-2">
                        @error('admission_number')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Roll Number</label>
                        <input type="text" name="roll_number" value="{{ old('roll_number', $student->roll_number) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="col-span-full">
                        <label class="block font-medium">Profile Photo</label>
                        @if($student->profile_photo)
                            <div class="mb-2">
                                <img src="{{ Storage::url($student->profile_photo) }}" class="h-20 w-auto" alt="Current Photo">
                                <span class="text-sm text-gray-500">Current file</span>
                            </div>
                        @endif
                        <input type="file" name="profile_photo" accept="image/*" class="w-full">
                        @error('profile_photo')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </fieldset>

            {{-- PARENT DETAILS --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">👪 Parent Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Father's Name <span class="text-red-500">*</span></label>
                        <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" class="w-full border rounded px-3 py-2">
                        @error('father_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Father's Phone</label>
                        <input type="text" name="father_phone" value="{{ old('father_phone', $student->father_phone) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Father's Occupation</label>
                        <input type="text" name="father_occupation" value="{{ old('father_occupation', $student->father_occupation) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Mother's Name</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Mother's Phone</label>
                        <input type="text" name="mother_phone" value="{{ old('mother_phone', $student->mother_phone) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Mother's Occupation</label>
                        <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $student->mother_occupation) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Parent ID Proof</label>
                        @if($student->parent_id_proof)
                            <div class="mb-1"><a href="{{ Storage::url($student->parent_id_proof) }}" target="_blank" class="text-blue-600 text-sm">View Current File</a></div>
                        @endif
                        <input type="file" name="parent_id_proof" accept="image/*,pdf">
                        @error('parent_id_proof')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Parent Signature</label>
                        @if($student->parent_signature)
                            <div class="mb-1"><img src="{{ Storage::url($student->parent_signature) }}" class="h-12 w-auto" alt="Signature"></div>
                        @endif
                        <input type="file" name="parent_signature" accept="image/*">
                        @error('parent_signature')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </fieldset>

            {{-- CONCESSION --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🎓 Concession</legend>
                <div>
                    <label class="block font-medium">Assign Concession</label>
                    <select name="assigned_concession" class="w-full md:w-1/2 border rounded px-3 py-2">
                        <option value="">Select Concession</option>
                        <option value="Merit Scholarship - 20%" {{ old('assigned_concession', $student->assigned_concession) == 'Merit Scholarship - 20%' ? 'selected' : '' }}>Merit Scholarship - 20%</option>
                        <option value="Need Based - 30%" {{ old('assigned_concession', $student->assigned_concession) == 'Need Based - 30%' ? 'selected' : '' }}>Need Based - 30%</option>
                        <option value="Sibling Concession - 10%" {{ old('assigned_concession', $student->assigned_concession) == 'Sibling Concession - 10%' ? 'selected' : '' }}>Sibling Concession - 10%</option>
                        <option value="Staff Ward - 50%" {{ old('assigned_concession', $student->assigned_concession) == 'Staff Ward - 50%' ? 'selected' : '' }}>Staff Ward - 50%</option>
                        <option value="None" {{ old('assigned_concession', $student->assigned_concession) == 'None' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
            </fieldset>

            {{-- STATUS & SUSPENSION --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">📌 Status & Suspension</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full border rounded px-3 py-2">
                            <option value="Active" {{ old('status', $student->status) == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status', $student->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div></div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="suspendCheckbox" {{ $student->suspension_start_date ? 'checked' : '' }}> Suspend Student
                        </label>
                    </div>
                </div>

                <div id="suspensionFields" style="{{ $student->suspension_start_date ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-medium">Start Date</label>
                        <input type="date" name="suspension_start_date" value="{{ old('suspension_start_date', $student->suspension_start_date ? $student->suspension_start_date->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">End Date</label>
                        <input type="date" name="suspension_end_date" value="{{ old('suspension_end_date', $student->suspension_end_date ? $student->suspension_end_date->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2">
                        @error('suspension_end_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Suspension Message</label>
                        <input type="text" name="suspension_message" value="{{ old('suspension_message', $student->suspension_message) }}" class="w-full border rounded px-3 py-2" placeholder="Enter suspension message">
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Update Student</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('suspendCheckbox').addEventListener('change', function(e) {
        document.getElementById('suspensionFields').style.display = e.target.checked ? 'grid' : 'none';
    });
</script>
@endpush
@endsection