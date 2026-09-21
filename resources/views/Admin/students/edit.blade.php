{{-- resources/views/admin/students/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Student — {{ $student->first_name }} {{ $student->last_name }}</h2>

        <form method="POST" action="{{ route('admin.students.update', $student->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender', $student->gender)=='Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $student->gender)=='Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block font-medium">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
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
                            @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
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
                        @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
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
                            @foreach(['General','OBC','SC','ST','EWS'] as $cat)
                                <option value="{{ $cat }}" {{ old('previous_category', $student->previous_category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
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
                        <input type="date" name="admission_date" value="{{ old('admission_date', optional($student->admission_date)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                        @error('admission_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Student Type <span class="text-red-500">*</span></label>
                        <input type="text" name="student_type" value="{{ old('student_type', $student->student_type) }}" placeholder="New / Transfer" class="w-full border rounded px-3 py-2">
                        @error('student_type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>

                    {{-- CLASS SELECTION --}}
                    <div>
                        <label class="block font-medium">Class <span class="text-red-500">*</span></label>
                        <select name="class_id" id="classSelect" class="w-full border rounded px-3 py-2">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ old('class_id', optional($student->classSection)->class_id) == $class->id ? 'selected' : '' }}>
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

                        <input type="text" name="section" id="sectionInput"
                               value="{{ old('section', optional($student->classSection)->section_name) }}"
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
                                <img src="{{ Storage::url($student->profile_photo) }}" alt="Current photo" class="h-20 w-20 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="profile_photo" accept="image/*" class="w-full">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep the current photo.</p>
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
                            <p class="text-xs mb-1"><a href="{{ Storage::url($student->parent_id_proof) }}" target="_blank" class="text-blue-600 underline">View current file</a></p>
                        @endif
                        <input type="file" name="parent_id_proof" accept="image/*,pdf">
                        @error('parent_id_proof')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Parent Signature</label>
                        @if($student->parent_signature)
                            <p class="text-xs mb-1"><a href="{{ Storage::url($student->parent_signature) }}" target="_blank" class="text-blue-600 underline">View current file</a></p>
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
                        @foreach([
                            'Merit Scholarship - 20%',
                            'Need Based - 30%',
                            'Sibling Concession - 10%',
                            'Staff Ward - 50%',
                            'None',
                        ] as $concession)
                            <option value="{{ $concession }}" {{ old('assigned_concession', $student->assigned_concession) == $concession ? 'selected' : '' }}>
                                {{ $concession }}
                            </option>
                        @endforeach
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
                            <option value="Active" {{ old('status', $student->status)=='Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status', $student->status)=='Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div></div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="suspendCheckbox" {{ old('suspension_start_date', $student->suspension_start_date) ? 'checked' : '' }}> Suspend Student
                        </label>
                    </div>
                </div>

                <div id="suspensionFields" style="{{ old('suspension_start_date', $student->suspension_start_date) ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-medium">Start Date</label>
                        <input type="date" name="suspension_start_date" value="{{ old('suspension_start_date', optional($student->suspension_start_date)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">End Date</label>
                        <input type="date" name="suspension_end_date" value="{{ old('suspension_end_date', optional($student->suspension_end_date)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                        @error('suspension_end_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Suspension Message</label>
                        <input type="text" name="suspension_message" value="{{ old('suspension_message', $student->suspension_message) }}" class="w-full border rounded px-3 py-2" placeholder="Enter suspension message">
                    </div>
                </div>
            </fieldset>

            {{-- TRANSPORT (OPTIONAL) --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🚌 Transport (Optional)</legend>

                <label class="inline-flex items-center mb-3">
                    <input type="checkbox" name="transport_enabled" value="1" id="transportEnabled"
                        {{ old('transport_enabled', $currentTransport ? 1 : 0) ? 'checked' : '' }}>
                    <span class="ml-2 font-medium">Assign this student to a transport route</span>
                </label>

                <div id="transportFields" style="{{ old('transport_enabled', $currentTransport ? 1 : 0) ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium">Route</label>
                        <select name="route_id" id="routeSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Route --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}"
                                    {{ old('route_id', optional($currentTransport)->route_id) == $route->id ? 'selected' : '' }}>
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
                            @if($currentTransport && $currentTransport->routeStop)
                                <option value="{{ $currentTransport->routeStop->id }}" selected>
                                    {{ $currentTransport->routeStop->stop_name }}
                                </option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium">Transport Fee Type</label>
                        <select name="transport_fee_type_id" id="transportFeeTypeSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- No fee record (skip) --</option>
                            @foreach($transportFeeTypes as $ft)
                                <option value="{{ $ft->id }}"
                                    {{ old('transport_fee_type_id', optional($currentTransport)->transport_fee_type_id) == $ft->id ? 'selected' : '' }}>
                                    {{ $ft->name }} - {{ $ft->period }} - PKR {{ number_format($ft->amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Changing this only affects new payments going forward.</p>
                    </div>
                </div>
            </fieldset>

            {{-- HOSTEL (OPTIONAL) --}}
            <fieldset class="border p-4 mb-6 rounded-lg">
                <legend class="font-bold text-lg px-2">🏠 Hostel (Optional)</legend>

                <label class="inline-flex items-center mb-3">
                    <input type="checkbox" name="hostel_enabled" value="1" id="hostelEnabled"
                        {{ old('hostel_enabled', $currentHostelAllocation ? 1 : 0) ? 'checked' : '' }}>
                    <span class="ml-2 font-medium">Allocate this student to a hostel room</span>
                </label>

                @if(session('hostel_error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2 rounded mb-3">{{ session('hostel_error') }}</div>
                @endif

                <div id="hostelFields" style="{{ old('hostel_enabled', $currentHostelAllocation ? 1 : 0) ? '' : 'display:none;' }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-medium">Hostel</label>
                        <select name="hostel_id" id="hostelSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Hostel --</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}"
                                    {{ old('hostel_id', optional($currentHostelAllocation)->hostel_id) == $hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Room (vacancy only)</label>
                        <select name="room_id" id="roomSelect" class="w-full border rounded px-3 py-2">
                            @if($currentHostelAllocation)
                                <option value="">-- Select Room --</option>
                                @foreach($currentRooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ old('room_id', $currentHostelAllocation->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }} ({{ $room->current_occupancy }}/{{ $room->capacity }})
                                    </option>
                                @endforeach
                            @else
                                <option value="">-- Select Hostel First --</option>
                            @endif
                        </select>
                        @error('room_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block font-medium">Bed Number (optional)</label>
                        <input type="text" name="bed_number" value="{{ old('bed_number', optional($currentHostelAllocation)->bed_number) }}" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-medium">Hostel Fee Type</label>
                        <select name="hostel_fee_type_id" id="hostelFeeTypeSelect" class="w-full border rounded px-3 py-2">
                            <option value="">-- No fee record (skip) --</option>
                            @foreach($hostelFeeTypes as $ft)
                                <option value="{{ $ft->id }}"
                                    {{ old('hostel_fee_type_id', optional($currentHostelAllocation)->hostel_fee_type_id) == $ft->id ? 'selected' : '' }}>
                                    {{ $ft->name }} - {{ $ft->period }} - PKR {{ number_format($ft->amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Changing this only affects new payments going forward.</p>
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.students.show', $student->id) }}" class="px-4 py-2 bg-gray-200 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Update Student</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // BUILD CLASS/SECTION LOOKUP DATA CLIENT-SIDE
    // (edit() doesn't pass classSectionsData/classesData like create()
    // does, so we derive the same shape here from $classSections/$classes,
    // which the controller already sends to this view.)
    // ============================================================
    const classSectionsRaw = @json($classSections);
    const classesRaw = @json($classes);

    const classSectionsData = {};
    classSectionsRaw.forEach(function (sec) {
        if (!classSectionsData[sec.class_id]) {
            classSectionsData[sec.class_id] = [];
        }
        classSectionsData[sec.class_id].push({
            section_name: sec.section_name,
            id: sec.id
        });
    });

    const classesData = classesRaw.map(function (c) {
        return {
            id: c.id,
            grade_name: c.grade ? c.grade.name : null
        };
    });

    // The section currently saved for this student (so we can preselect it
    // in the dropdown once its class's sections are populated).
    const currentSectionName = @json(old('section', optional($student->classSection)->section_name));

    // ============================================================
    // DOM ELEMENTS
    // ============================================================
    const classSelect   = document.getElementById('classSelect');
    const sectionInput  = document.getElementById('sectionInput');
    const sectionSelect = document.getElementById('sectionSelect');

    // ============================================================
    // POPULATE SECTIONS (real <select> — reliable across all browsers)
    // ============================================================
    function populateSections(preselect) {
        if (!classSelect || !sectionSelect) return;

        const classId = classSelect.value;

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

        const sections = classSectionsData[classId] || [];

        if (sections.length > 0) {
            emptyOpt.textContent = '-- Select existing section --';
            sections.forEach(function (sec) {
                const opt = document.createElement('option');
                opt.value = sec.section_name;
                opt.textContent = sec.section_name;
                if (preselect && sec.section_name === preselect) {
                    opt.selected = true;
                }
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

    if (sectionSelect) {
        sectionSelect.addEventListener('change', function () {
            if (this.value && sectionInput) {
                sectionInput.value = this.value;
            }
        });
    }

    // ============================================================
    // ATTACH EVENT LISTENERS
    // ============================================================
    if (classSelect) {
        classSelect.addEventListener('change', function () {
            populateSections(null); // manual class change -> don't force old section
        });

        // On initial page load, populate for the student's current class
        // and preselect their existing section in the dropdown.
        if (classSelect.value) {
            populateSections(currentSectionName);
        }
    }

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
    const routesData = @json($routes->map(function ($r) {
        return [
            'id' => $r->id,
            'stops' => $r->stops->map(function ($s) {
                return ['id' => $s->id, 'stop_name' => $s->stop_name];
            }),
        ];
    }));
    const routeSelect = document.getElementById('routeSelect');
    const stopSelect = document.getElementById('stopSelect');
    const currentStopId = @json(optional($currentTransport)->route_stop_id);

    function populateStops(preselectId) {
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
                    if (preselectId && String(stop.id) === String(preselectId)) {
                        opt.selected = true;
                    }
                    stopSelect.appendChild(opt);
                });
            }
        }
    }

    if (routeSelect) {
        routeSelect.addEventListener('change', function () {
            populateStops(null);
        });
        if (routeSelect.value) {
            populateStops(currentStopId);
        }
    }

    // ============================================================
    // HOSTEL: HOSTEL -> ROOMS (AJAX)
    // ============================================================
    const hostelSelect = document.getElementById('hostelSelect');
    const roomSelect = document.getElementById('roomSelect');
    const currentRoomId = @json(optional($currentHostelAllocation)->room_id);
    let hostelFieldsInitialized = false;

    function loadRooms(preselectId) {
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
                if (rooms.length === 0 && !preselectId) {
                    roomSelect.innerHTML = '<option value="">No rooms with vacancy</option>';
                    return;
                }
                roomSelect.innerHTML = '<option value="">-- Select Room --</option>';
                rooms.forEach(function (room) {
                    var opt = document.createElement('option');
                    opt.value = room.id;
                    opt.textContent = room.room_number + ' (' + room.current_occupancy + '/' + room.capacity + ')';
                    if (preselectId && String(room.id) === String(preselectId)) {
                        opt.selected = true;
                    }
                    roomSelect.appendChild(opt);
                });
            })
            .catch(function(error) {
                console.error('Error loading rooms:', error);
                roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
            });
    }

    if (hostelSelect) {
        hostelSelect.addEventListener('change', function () {
            // Once the user actively changes the hostel, refresh via AJAX.
            loadRooms(hostelFieldsInitialized ? null : currentRoomId);
            hostelFieldsInitialized = true;
        });
        // Don't overwrite the server-rendered room list (which already
        // contains the student's current room) on initial page load.
    }

});
</script>
@endpush
@endsection