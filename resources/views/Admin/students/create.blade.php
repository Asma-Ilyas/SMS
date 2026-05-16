{{-- resources/views/admin/students/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Admission')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Student Admission Form</h2>

        <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- PERSONAL DETAILS --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">👤 Personal Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border rounded px-3 py-2">
                        @error('first_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border rounded px-3 py-2">
                        @error('last_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" class="w-full border rounded px-3 py-2">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender')=='Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender')=='Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full border rounded px-3 py-2">
                        @error('date_of_birth')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">DOB in words</label>
                        <input type="text" name="dob_in_words" value="{{ old('dob_in_words') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Religion</label>
                        <input type="text" name="religion" value="{{ old('religion') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Caste / Sub Caste</label>
                        <input type="text" name="caste_subcaste" value="{{ old('caste_subcaste') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Blood Group</label>
                        <select name="blood_group" class="w-full border rounded px-3 py-2">
                            <option value="">Select</option>
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                        </select>
                    </div>

                    <div class="col-span-full">
                        <label class="block font-medium">Address</label>
                        <textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">State</label>
                        <input type="text" name="state" value="{{ old('state') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Country</label>
                        <input type="text" name="country" value="{{ old('country') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="col-span-full">
                        <label class="block font-medium">Extra Note / Detail</label>
                        <textarea name="extra_note" rows="2" class="w-full border rounded px-3 py-2">{{ old('extra_note') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-medium">Mother Tongue</label>
                        <input type="text" name="mother_tongue" value="{{ old('mother_tongue') }}" class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block font-medium">Birth Place</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </fieldset>

            {{-- PREVIOUS SCHOOL DETAILS --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🏫 Previous School Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">School Name</label>
                        <input type="text" name="previous_school_name" value="{{ old('previous_school_name') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">School Address</label>
                        <input type="text" name="previous_school_address" value="{{ old('previous_school_address') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Class</label>
                        <input type="text" name="previous_class" value="{{ old('previous_class') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Passout Year</label>
                        <input type="text" name="passout_year" value="{{ old('passout_year') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Category</label>
                        <select name="previous_category" class="w-full border rounded px-3 py-2">
                            <option value="">Select Category</option>
                            <option value="General" {{ old('previous_category')=='General' ? 'selected' : '' }}>General</option>
                            <option value="OBC" {{ old('previous_category')=='OBC' ? 'selected' : '' }}>OBC</option>
                            <option value="SC" {{ old('previous_category')=='SC' ? 'selected' : '' }}>SC</option>
                            <option value="ST" {{ old('previous_category')=='ST' ? 'selected' : '' }}>ST</option>
                            <option value="EWS" {{ old('previous_category')=='EWS' ? 'selected' : '' }}>EWS</option>
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
                        <input type="date" name="admission_date" value="{{ old('admission_date') }}" class="w-full border rounded px-3 py-2">
                        @error('admission_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Student Type <span class="text-red-500">*</span></label>
                        <input type="text" name="student_type" value="{{ old('student_type') }}" placeholder="New / Transfer" class="w-full border rounded px-3 py-2">
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
                        <input type="text" name="section" value="{{ old('section') }}" class="w-full border rounded px-3 py-2">
                        @error('section')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Admission Number <span class="text-red-500">*</span></label>
                        <input type="text" name="admission_number" value="{{ old('admission_number') }}" class="w-full border rounded px-3 py-2">
                        @error('admission_number')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Roll Number</label>
                        <input type="text" name="roll_number" value="{{ old('roll_number') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="col-span-full">
                        <label class="block font-medium">Profile Photo</label>
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
                        <input type="text" name="father_name" value="{{ old('father_name') }}" class="w-full border rounded px-3 py-2">
                        @error('father_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Father's Phone</label>
                        <input type="text" name="father_phone" value="{{ old('father_phone') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Father's Occupation</label>
                        <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Mother's Name</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Mother's Phone</label>
                        <input type="text" name="mother_phone" value="{{ old('mother_phone') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Mother's Occupation</label>
                        <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Parent ID Proof</label>
                        <input type="file" name="parent_id_proof" accept="image/*,pdf">
                        @error('parent_id_proof')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Parent Signature</label>
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
                        <option value="Merit Scholarship - 20%" {{ old('assigned_concession')=='Merit Scholarship - 20%' ? 'selected' : '' }}>Merit Scholarship - 20%</option>
                        <option value="Need Based - 30%" {{ old('assigned_concession')=='Need Based - 30%' ? 'selected' : '' }}>Need Based - 30%</option>
                        <option value="Sibling Concession - 10%" {{ old('assigned_concession')=='Sibling Concession - 10%' ? 'selected' : '' }}>Sibling Concession - 10%</option>
                        <option value="Staff Ward - 50%" {{ old('assigned_concession')=='Staff Ward - 50%' ? 'selected' : '' }}>Staff Ward - 50%</option>
                        <option value="None" {{ old('assigned_concession')=='None' ? 'selected' : '' }}>None</option>
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
                            <option value="Active" {{ old('status')=='Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status')=='Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div></div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="suspendCheckbox" {{ old('suspension_start_date') ? 'checked' : '' }}> Suspend Student
                        </label>
                    </div>
                </div>

                <div id="suspensionFields" style="{{ old('suspension_start_date') ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-medium">Start Date</label>
                        <input type="date" name="suspension_start_date" value="{{ old('suspension_start_date') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">End Date</label>
                        <input type="date" name="suspension_end_date" value="{{ old('suspension_end_date') }}" class="w-full border rounded px-3 py-2">
                        @error('suspension_end_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Suspension Message</label>
                        <input type="text" name="suspension_message" value="{{ old('suspension_message') }}" class="w-full border rounded px-3 py-2" placeholder="Enter suspension message">
                    </div>
                </div>
            </fieldset>

            {{-- FEE MANAGEMENT SECTION (Updated) --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">💰 Fee Management (Optional)</legend>
                
                <div id="feeItemsContainer">
                    <div class="fee-item grid grid-cols-1 md:grid-cols-4 gap-3 mb-2 items-end">
                        <div>
                            <label class="block font-medium">Fee Type</label>
                            <select name="fee_items[0][fee_submission_type_id]" class="w-full border rounded px-3 py-2">
                                <option value="">Select Fee Type</option>
                                @foreach($feeTypes as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }} - {{ ucfirst($type->period) }} - PKR {{ number_format($type->amount,2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Period Override</label>
                            <select name="fee_items[0][period]" class="w-full border rounded px-3 py-2">
                                <option value="">Use Default</option>
                                <option value="monthly">Monthly</option>
                                {{-- Quarterly removed --}}
                                <option value="annually">Annually</option>
                                <option value="one_time">One Time</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Amount (PKR)</label>
                            <input type="number" step="0.01" name="fee_items[0][amount]" class="w-full border rounded px-3 py-2" placeholder="Leave empty to use default">
                        </div>
                        <div>
                            <label class="block font-medium">Installments</label>
                            <input type="number" name="fee_items[0][installment_count]" min="1" max="12" class="w-full border rounded px-3 py-2" placeholder="1 = one-time, 2+ = installments">
                        </div>
                    </div>
                </div>
                
                <button type="button" id="addFeeItem" class="mt-2 bg-blue-100 px-4 py-2 rounded">+ Add Another Fee</button>
            </fieldset>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Save Student</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let feeIndex = 1;
    document.getElementById('addFeeItem').addEventListener('click', function() {
        let container = document.getElementById('feeItemsContainer');
        let newItem = document.createElement('div');
        newItem.className = 'fee-item grid grid-cols-1 md:grid-cols-4 gap-3 mb-2 items-end';
        newItem.innerHTML = `
            <div>
                <label class="block font-medium">Fee Type</label>
                <select name="fee_items[${feeIndex}][fee_submission_type_id]" class="w-full border rounded px-3 py-2">
                    <option value="">Select Fee Type</option>
                    @foreach($feeTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }} - {{ ucfirst($type->period) }} - PKR {{ number_format($type->amount,2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-medium">Period Override</label>
                <select name="fee_items[${feeIndex}][period]" class="w-full border rounded px-3 py-2">
                    <option value="">Use Default</option>
                    <option value="monthly">Monthly</option>
                    <option value="annually">Annually</option>
                    <option value="one_time">One Time</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Amount (PKR)</label>
                <input type="number" step="0.01" name="fee_items[${feeIndex}][amount]" class="w-full border rounded px-3 py-2" placeholder="Leave empty to use default">
            </div>
            <div>
                <label class="block font-medium">Installments</label>
                <input type="number" name="fee_items[${feeIndex}][installment_count]" min="1" max="12" class="w-full border rounded px-3 py-2" placeholder="1 = one-time, 2+ = installments">
            </div>
            <button type="button" class="removeFeeItem bg-red-100 text-red-700 px-3 py-2 rounded-md h-10">Remove</button>
        `;
        container.appendChild(newItem);
        attachRemoveEvents();
        feeIndex++;
    });
    
    function attachRemoveEvents() {
        document.querySelectorAll('.removeFeeItem').forEach(btn => {
            btn.removeEventListener('click', removeHandler);
            btn.addEventListener('click', removeHandler);
        });
    }
    function removeHandler(e) {
        e.target.closest('.fee-item').remove();
    }
    attachRemoveEvents();

    document.getElementById('suspendCheckbox').addEventListener('change', function(e) {
        document.getElementById('suspensionFields').style.display = e.target.checked ? 'grid' : 'none';
    });
</script>
@endpush
@endsection