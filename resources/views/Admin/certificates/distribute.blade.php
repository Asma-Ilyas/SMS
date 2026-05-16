{{-- resources/views/admin/certificates/distribute.blade.php --}}
@extends('layouts.app')

@section('title', 'Distribute Certificate')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Distribute: {{ $certificateType->title }}</h2>

        <form method="POST" action="{{ route('admin.certificates.distribute.store', $certificateType) }}" enctype="multipart/form-data">
            @csrf

            {{-- Step 1: Class --}}
            <div class="mb-4">
                <label class="block font-medium">Class <span class="text-red-500">*</span></label>
                <select id="class_id" name="class_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Step 2: Section --}}
            <div class="mb-4">
                <label class="block font-medium">Section</label>
                <select id="section" name="section" class="w-full border rounded px-3 py-2" disabled>
                    <option value="">First select a class</option>
                </select>
            </div>

            {{-- Step 3: Student --}}
            <div class="mb-4">
                <label class="block font-medium">Student <span class="text-red-500">*</span></label>
                <select id="student_id" name="student_id" class="w-full border rounded px-3 py-2" disabled required>
                    <option value="">First select a class and section</option>
                </select>
                @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            {{-- Display selected student info (optional) --}}
            <div id="studentInfo" class="mb-4 p-3 bg-gray-50 rounded hidden">
                <h4 class="font-semibold">Selected Student Details</h4>
                <p id="studentDetail"></p>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Issue Date <span class="text-red-500">*</span></label>
                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Certificate File (Optional)</label>
                <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                <p class="text-xs text-gray-500">Upload a custom certificate for this student.</p>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Remarks (Optional)</label>
                <textarea name="remarks" rows="3" class="w-full border rounded px-3 py-2">{{ old('remarks') }}</textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 bg-gray-300 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Issue Certificate</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // When class changes, load sections
        $('#class_id').change(function() {
            let classId = $(this).val();
            let sectionSelect = $('#section');
            let studentSelect = $('#student_id');
            if (classId) {
                $.get(`/admin/sections/${classId}`, function(data) {
                    sectionSelect.prop('disabled', false).empty().append('<option value="">Select Section</option>');
                    if (data.length === 0) {
                        sectionSelect.append('<option value="all">All Sections</option>');
                    } else {
                        $.each(data, function(i, section) {
                            sectionSelect.append(`<option value="${section}">${section}</option>`);
                        });
                    }
                    studentSelect.prop('disabled', true).empty().append('<option value="">Select Section First</option>');
                    $('#studentInfo').addClass('hidden');
                });
            } else {
                sectionSelect.prop('disabled', true).empty().append('<option value="">First select a class</option>');
                studentSelect.prop('disabled', true).empty().append('<option value="">First select a class</option>');
            }
        });

        // When section changes, load students
        $('#section').change(function() {
            let classId = $('#class_id').val();
            let section = $(this).val();
            let studentSelect = $('#student_id');
            if (classId && section) {
                $.get(`/admin/students/${classId}/${section}`, function(data) {
                    studentSelect.prop('disabled', false).empty().append('<option value="">Select Student</option>');
                    $.each(data, function(i, student) {
                        studentSelect.append(`<option value="${student.id}">${student.name} (${student.admission_number})</option>`);
                    });
                    $('#studentInfo').addClass('hidden');
                });
            } else {
                studentSelect.prop('disabled', true).empty().append('<option value="">First select a class and section</option>');
            }
        });

        // When student changes, show details
        $('#student_id').change(function() {
            let studentId = $(this).val();
            if (studentId) {
                $.get(`/admin/student-info/${studentId}`, function(data) {
                    $('#studentDetail').html(`
                        <strong>Name:</strong> ${data.name}<br>
                        <strong>Admission No:</strong> ${data.admission_number}<br>
                        <strong>Class:</strong> ${data.class}<br>
                        <strong>Section:</strong> ${data.section}
                    `);
                    $('#studentInfo').removeClass('hidden');
                });
            } else {
                $('#studentInfo').addClass('hidden');
            }
        });
    });
</script>
@endpush
@endsection