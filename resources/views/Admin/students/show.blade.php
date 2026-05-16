{{-- resources/views/admin/students/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Student Details</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Edit</a>
                <a href="{{ route('admin.students.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md">Back to List</a>
            </div>
        </div>

        {{-- Profile Photo --}}
        @if($student->profile_photo)
            <div class="flex justify-center mb-6">
                <img src="{{ Storage::url($student->profile_photo) }}" class="h-32 w-32 rounded-full object-cover border-4 border-gray-200" alt="Profile Photo">
            </div>
        @endif

        {{-- PERSONAL DETAILS --}}
        <div class="border rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold bg-gray-100 -mt-4 -mx-4 px-4 py-2 rounded-t-lg">👤 Personal Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div><strong>Full Name:</strong> {{ $student->full_name }}</div>
                <div><strong>Gender:</strong> {{ $student->gender }}</div>
                <div><strong>Date of Birth:</strong> {{ $student->date_of_birth->format('d-m-Y') }}</div>
                <div><strong>DOB in words:</strong> {{ $student->dob_in_words ?? 'N/A' }}</div>
                <div><strong>Religion:</strong> {{ $student->religion ?? 'N/A' }}</div>
                <div><strong>Caste/Sub Caste:</strong> {{ $student->caste_subcaste ?? 'N/A' }}</div>
                <div><strong>Blood Group:</strong> {{ $student->blood_group ?? 'N/A' }}</div>
                <div><strong>Mother Tongue:</strong> {{ $student->mother_tongue ?? 'N/A' }}</div>
                <div><strong>Birth Place:</strong> {{ $student->birth_place ?? 'N/A' }}</div>
                <div><strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}</div>
                <div><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</div>
                <div><strong>Address:</strong> {{ $student->address ?? 'N/A' }}</div>
                <div><strong>City:</strong> {{ $student->city ?? 'N/A' }}</div>
                <div><strong>State:</strong> {{ $student->state ?? 'N/A' }}</div>
                <div><strong>Country:</strong> {{ $student->country ?? 'N/A' }}</div>
                <div class="col-span-full"><strong>Extra Note:</strong> {{ $student->extra_note ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- PREVIOUS SCHOOL DETAILS --}}
        <div class="border rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold bg-gray-100 -mt-4 -mx-4 px-4 py-2 rounded-t-lg">🏫 Previous School Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div><strong>School Name:</strong> {{ $student->previous_school_name ?? 'N/A' }}</div>
                <div><strong>School Address:</strong> {{ $student->previous_school_address ?? 'N/A' }}</div>
                <div><strong>Class:</strong> {{ $student->previous_class ?? 'N/A' }}</div>
                <div><strong>Passout Year:</strong> {{ $student->passout_year ?? 'N/A' }}</div>
                <div><strong>Category:</strong> {{ $student->previous_category ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- ADMISSION DETAILS --}}
        <div class="border rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold bg-gray-100 -mt-4 -mx-4 px-4 py-2 rounded-t-lg">📅 Admission Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div><strong>Admission Date:</strong> {{ $student->admission_date->format('d-m-Y') }}</div>
                <div><strong>Student Type:</strong> {{ $student->student_type }}</div>
                <div><strong>Class:</strong> {{ $student->class->full_name ?? $student->class->name }}</div>
                <div><strong>Section:</strong> {{ $student->section }}</div>
                <div><strong>Admission Number:</strong> {{ $student->admission_number }}</div>
                <div><strong>Roll Number:</strong> {{ $student->roll_number ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- PARENT DETAILS --}}
        <div class="border rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold bg-gray-100 -mt-4 -mx-4 px-4 py-2 rounded-t-lg">👪 Parent Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div><strong>Father's Name:</strong> {{ $student->father_name }}</div>
                <div><strong>Father's Phone:</strong> {{ $student->father_phone ?? 'N/A' }}</div>
                <div><strong>Father's Occupation:</strong> {{ $student->father_occupation ?? 'N/A' }}</div>
                <div><strong>Mother's Name:</strong> {{ $student->mother_name ?? 'N/A' }}</div>
                <div><strong>Mother's Phone:</strong> {{ $student->mother_phone ?? 'N/A' }}</div>
                <div><strong>Mother's Occupation:</strong> {{ $student->mother_occupation ?? 'N/A' }}</div>
                <div><strong>Parent ID Proof:</strong> 
                    @if($student->parent_id_proof)
                        <a href="{{ Storage::url($student->parent_id_proof) }}" target="_blank" class="text-blue-600">View File</a>
                    @else N/A @endif
                </div>
                <div><strong>Parent Signature:</strong>
                    @if($student->parent_signature)
                        <img src="{{ Storage::url($student->parent_signature) }}" class="h-12 w-auto inline-block">
                    @else N/A @endif
                </div>
            </div>
        </div>

        {{-- CONCESSION --}}
        <div class="border rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold bg-gray-100 -mt-4 -mx-4 px-4 py-2 rounded-t-lg">🎓 Concession</h3>
            <div class="mt-3">
                <strong>Assigned Concession:</strong> {{ $student->assigned_concession ?? 'None' }}
            </div>
        </div>

        {{-- STATUS & SUSPENSION --}}
        <div class="border rounded-lg p-4 mb-6">
            <h3 class="text-lg font-bold bg-gray-100 -mt-4 -mx-4 px-4 py-2 rounded-t-lg">📌 Status & Suspension</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div><strong>Status:</strong> 
                    <span class="px-2 py-1 rounded text-sm {{ $student->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $student->status }}
                    </span>
                </div>
                @if($student->suspension_start_date)
                    <div><strong>Suspension Start:</strong> {{ $student->suspension_start_date->format('d-m-Y') }}</div>
                    <div><strong>Suspension End:</strong> {{ $student->suspension_end_date ? $student->suspension_end_date->format('d-m-Y') : 'N/A' }}</div>
                    <div class="col-span-full"><strong>Suspension Message:</strong> {{ $student->suspension_message ?? 'N/A' }}</div>
                @else
                    <div class="col-span-full">No suspension record.</div>
                @endif
            </div>
        </div>

        <div class="flex justify-end">
            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md">Delete Student</button>
            </form>
        </div>
        <a href="{{ route('admin.students.transfer-out.form', $student) }}" class="bg-red-600 text-white px-4 py-2 rounded">Transfer Out</a>
    </div>
</div>
@endsection