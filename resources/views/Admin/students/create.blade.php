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

                    {{-- CLASS SELECTION --}}
                    <div>
                        <label class="block font-medium">Class <span class="text-red-500">*</span></label>
                        <select name="class_id" id="classSelect" class="w-full border rounded px-3 py-2">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->grade ? $class->grade->name : 'Class ' . $class->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    {{-- SECTION SELECTION (real <select> + free-text fallback) --}}
                    <div>
                        <label class="block font-medium">Section <span class="text-red-500">*</span></label>

                        <select id="sectionSelect" class="w-full border rounded px-3 py-2 mb-2">
                            <option value="">-- Select class first --</option>
                        </select>

                        <input type="text" name="section" id="sectionInput" value="{{ old('section') }}"
                               autocomplete="off"
                               placeholder="Select class first"
                               class="w-full border rounded px-3 py-2">

                        <p class="text-xs text-gray-500 mt-1">
                            Pick an existing section above, or type a new section name in the box to create one.
                        </p>
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

            {{-- TRANSPORT (OPTIONAL) --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🚌 Transport (Optional)</legend>

                <label class="inline-flex items-center mb-3">
                    <input type="checkbox" name="transport_enabled" value="1" id="transportEnabled" {{ old('transport_enabled') ? 'checked' : '' }}>
                    <span class="ml-2 font-medium">Assign this student to a transport route</span>
                </label>

                <div id="transportFields" style="{{ old('transport_enabled') ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium">Route</label>
                        <select name="route_id" id="routeSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Route --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ old('route_id')==$route->id ? 'selected' : '' }}>
                                    {{ $route->name }} ({{ $route->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('route_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Pickup/Drop Stop</label>
                        <select name="route_stop_id" id="stopSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Stop --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Transport Fee Type</label>
                        <select name="transport_fee_type_id" id="transportFeeTypeSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- No fee record (skip) --</option>
                            @foreach($transportFeeTypes as $ft)
                                <option value="{{ $ft->id }}" {{ old('transport_fee_type_id')==$ft->id ? 'selected' : '' }}>
                                    {{ $ft->name }} - {{ $ft->period }} - PKR {{ number_format($ft->amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Selecting a fee type creates the first pending payment automatically.</p>
                    </div>
                </div>
            </fieldset>

            {{-- HOSTEL (OPTIONAL) --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🏠 Hostel (Optional)</legend>

                <label class="inline-flex items-center mb-3">
                    <input type="checkbox" name="hostel_enabled" value="1" id="hostelEnabled" {{ old('hostel_enabled') ? 'checked' : '' }}>
                    <span class="ml-2 font-medium">Allocate this student to a hostel room</span>
                </label>

                @if(session('hostel_error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2 rounded mb-3">{{ session('hostel_error') }}</div>
                @endif

                <div id="hostelFields" style="{{ old('hostel_enabled') ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-medium">Hostel</label>
                        <select name="hostel_id" id="hostelSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Hostel --</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ old('hostel_id')==$hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Room (vacancy only)</label>
                        <select name="room_id" id="roomSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Hostel First --</option>
                        </select>
                        @error('room_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Bed Number (optional)</label>
                        <input type="text" name="bed_number" value="{{ old('bed_number') }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Hostel Fee Type</label>
                        <select name="hostel_fee_type_id" id="hostelFeeTypeSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- No fee record (skip) --</option>
                            @foreach($hostelFeeTypes as $ft)
                                <option value="{{ $ft->id }}" {{ old('hostel_fee_type_id')==$ft->id ? 'selected' : '' }}>
                                    {{ $ft->name }} - {{ $ft->period }} - PKR {{ number_format($ft->amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Selecting a fee type creates the first pending payment automatically.</p>
                    </div>
                </div>
            </fieldset>

            {{-- FEE MANAGEMENT SECTION --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">💰 Fee Management (Optional)</legend>

                <div id="feeItemsContainer">
                    <div class="fee-item grid grid-cols-1 md:grid-cols-5 gap-3 mb-2 items-end">
                        <div>
                            <label class="block font-medium">Fee Type</label>
                            <select name="fee_items[0][fee_submission_type_id]" class="w-full border rounded px-3 py-2 fee-type-select">
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
                        <div class="flex items-end">
                            <button type="button" class="removeFeeItem bg-red-100 text-red-700 px-3 py-2 rounded-md h-10 hidden">Remove</button>
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
document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // DATA FROM CONTROLLER
    // ============================================================
    const classSectionsData = @json($classSectionsData);
    const classesData = @json($classesData);

    // ============================================================
    // DOM ELEMENTS
    // ============================================================
    const classSelect   = document.getElementById('classSelect');
    const sectionInput  = document.getElementById('sectionInput');
    const sectionSelect = document.getElementById('sectionSelect');

    // ============================================================
    // POPULATE SECTIONS (real <select> — reliable across all browsers)
    // ============================================================
    function populateSections() {
        if (!classSelect || !sectionSelect) return;

        const classId = classSelect.value;

        // reset the dropdown
        sectionSelect.innerHTML = '';
        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        sectionSelect.appendChild(emptyOpt);

        if (!classId) {
            emptyOpt.textContent = '-- Select class first --';
            if (sectionInput) {
                sectionInput.placeholder = 'Select class first';
            }
            return;
        }

        if (typeof classSectionsData === 'undefined') {
            emptyOpt.textContent = '-- Error loading sections --';
            return;
        }

        const sections = classSectionsData[classId] || [];

        if (sections.length > 0) {
            emptyOpt.textContent = '-- Select existing section --';
            sections.forEach(function (sec) {
                const opt = document.createElement('option');
                opt.value = sec.section_name;
                opt.textContent = sec.section_name;
                sectionSelect.appendChild(opt);
            });
            if (sectionInput) {
                sectionInput.placeholder = 'e.g. ' + sections[0].section_name;
            }
        } else {
            emptyOpt.textContent = '-- No existing sections --';
            if (sectionInput) {
                sectionInput.placeholder = 'Type new section (e.g. A)';
            }
        }
    }

    // When user picks a section from the dropdown, copy it into the
    // real text input that gets submitted with the form.
    if (sectionSelect) {
        sectionSelect.addEventListener('change', function () {
            if (this.value && sectionInput) {
                sectionInput.value = this.value;
            }
        });
    }

    // ============================================================
    // SUGGEST FEE FUNCTION
    // ============================================================
    function suggestFeeForClass() {
        if (!classSelect) return;

        const classId = classSelect.value;
        if (!classId) return;

        if (typeof classesData === 'undefined') return;

        const classInfo = classesData.find(function (c) {
            return String(c.id) === String(classId);
        });

        if (!classInfo || !classInfo.grade_name) return;

        const grade = classInfo.grade_name;

        let pattern = null;
        if (grade === 'KG') {
            pattern = '(KG)';
        } else if (['1', '2', '3', '4', '5'].includes(grade)) {
            pattern = '(Class 1-5)';
        } else if (['6', '7', '8'].includes(grade)) {
            pattern = '(Class 6-8)';
        } else if (['9', '10'].includes(grade)) {
            pattern = '(Class 9-10)';
        }

        if (!pattern) return;

        const firstFeeSelect = document.querySelector('select[name="fee_items[0][fee_submission_type_id]"]');
        if (!firstFeeSelect || firstFeeSelect.value) return;

        for (const opt of firstFeeSelect.options) {
            if (opt.textContent.includes(pattern)) {
                firstFeeSelect.value = opt.value;
                break;
            }
        }
    }

    // ============================================================
    // ATTACH EVENT LISTENERS
    // ============================================================
    if (classSelect) {
        classSelect.addEventListener('change', function () {
            populateSections();
            suggestFeeForClass();
        });

        // Run on load if class is pre-selected (e.g. validation redirect)
        if (classSelect.value) {
            populateSections();
        }
    }

    // ============================================================
    // FEE ITEMS MANAGEMENT
    // ============================================================
    let feeIndex = 1;
    const addFeeItemBtn = document.getElementById('addFeeItem');
    if (addFeeItemBtn) {
        addFeeItemBtn.addEventListener('click', function() {
            let container = document.getElementById('feeItemsContainer');
            let newItem = document.createElement('div');
            newItem.className = 'fee-item grid grid-cols-1 md:grid-cols-5 gap-3 mb-2 items-end';
            newItem.innerHTML = `
                <div>
                    <label class="block font-medium">Fee Type</label>
                    <select name="fee_items[${feeIndex}][fee_submission_type_id]" class="w-full border rounded px-3 py-2 fee-type-select">
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
                <div class="flex items-end">
                    <button type="button" class="removeFeeItem bg-red-100 text-red-700 px-3 py-2 rounded-md h-10">Remove</button>
                </div>
            `;
            container.appendChild(newItem);
            attachRemoveEvents();
            feeIndex++;
        });
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.removeFeeItem').forEach(btn => {
            btn.removeEventListener('click', removeHandler);
            btn.addEventListener('click', removeHandler);
        });
        updateRemoveButtons();
    }

    function removeHandler(e) {
        e.target.closest('.fee-item').remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.fee-item');
        items.forEach((item) => {
            const removeBtn = item.querySelector('.removeFeeItem');
            if (removeBtn) {
                if (items.length <= 1) {
                    removeBtn.classList.add('hidden');
                } else {
                    removeBtn.classList.remove('hidden');
                }
            }
        });
    }

    attachRemoveEvents();

    // ============================================================
    // SUSPENSION TOGGLE
    // ============================================================
    const suspendCheckbox = document.getElementById('suspendCheckbox');
    const suspensionFields = document.getElementById('suspensionFields');
    if (suspendCheckbox && suspensionFields) {
        suspendCheckbox.addEventListener('change', function(e) {
            suspensionFields.style.display = e.target.checked ? 'grid' : 'none';
        });
    }

    // ============================================================
    // TRANSPORT TOGGLE
    // ============================================================
    const transportEnabled = document.getElementById('transportEnabled');
    const transportFields = document.getElementById('transportFields');
    if (transportEnabled && transportFields) {
        transportEnabled.addEventListener('change', function(e) {
            transportFields.style.display = e.target.checked ? 'grid' : 'none';
        });
    }

    // ============================================================
    // HOSTEL TOGGLE
    // ============================================================
    const hostelEnabled = document.getElementById('hostelEnabled');
    const hostelFields = document.getElementById('hostelFields');
    if (hostelEnabled && hostelFields) {
        hostelEnabled.addEventListener('change', function(e) {
            hostelFields.style.display = e.target.checked ? 'grid' : 'none';
        });
    }

    // ============================================================
    // TRANSPORT: ROUTE -> STOPS
    // ============================================================
    const routesData = @json($routesData ?? []);
    const routeSelect = document.getElementById('routeSelect');
    const stopSelect = document.getElementById('stopSelect');

    function populateStops() {
        if (!routeSelect || !stopSelect) return;
        const routeId = routeSelect.value;
        stopSelect.innerHTML = '<option value="">-- Select Stop --</option>';
        if (routeId) {
            const route = routesData.find(r => String(r.id) === String(routeId));
            if (route && route.stops) {
                route.stops.forEach(function (stop) {
                    const opt = document.createElement('option');
                    opt.value = stop.id;
                    opt.textContent = stop.stop_name;
                    stopSelect.appendChild(opt);
                });
            }
        }
    }

    if (routeSelect) {
        routeSelect.addEventListener('change', populateStops);
        if (routeSelect.value) populateStops();
    }

    // ============================================================
    // HOSTEL: HOSTEL -> ROOMS (AJAX)
    // ============================================================
    const hostelSelect = document.getElementById('hostelSelect');
    const roomSelect = document.getElementById('roomSelect');

    function loadRooms() {
        if (!hostelSelect || !roomSelect) return;
        const hostelId = hostelSelect.value;
        roomSelect.innerHTML = '<option value="">Loading...</option>';

        if (!hostelId) {
            roomSelect.innerHTML = '<option value="">-- Select Hostel First --</option>';
            return;
        }

        fetch('/admin/hostels/' + hostelId + '/rooms')
            .then(function(response) {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(function(rooms) {
                roomSelect.innerHTML = '';
                if (rooms.length === 0) {
                    roomSelect.innerHTML = '<option value="">No rooms with vacancy</option>';
                    return;
                }
                roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
                rooms.forEach(function (room) {
                    var opt = document.createElement('option');
                    opt.value = room.id;
                    opt.textContent = room.room_number + ' (' + room.current_occupancy + '/' + room.capacity + ')';
                    roomSelect.appendChild(opt);
                });
            })
            .catch(function(error) {
                console.error('Error loading rooms:', error);
                roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
            });
    }

    if (hostelSelect) {
        hostelSelect.addEventListener('change', loadRooms);
        if (hostelSelect.value) loadRooms();
    }

});
</script>
@endpush
@endsection