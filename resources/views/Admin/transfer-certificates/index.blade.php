@extends('layouts.app')

@section('title', 'Transfer Certificates')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Transfer Certificates</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    {{-- List of issued certificates --}}
    <div class="bg-white rounded shadow overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th>Certificate #</th><th>Student</th><th>Admission No.</th><th>Class</th><th>Section</th><th>Certificate Type</th><th>Issued Date</th><th>Status After</th></tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                <tr>
                    <td>{{ $cert->certificate_number }}</td>
                    <td>{{ $cert->student->full_name }}</td>
                    <td>{{ $cert->student->admission_number }}</td>
                    <td>{{ $cert->student->class->grade->name ?? '' }} {{ $cert->student->class->stream->name ?? '' }}</td>
                    <td>{{ $cert->student->section }}</td>
                    <td>{{ implode(', ', $cert->certificate_types) }}</td>
                    <td>{{ $cert->issued_date->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($cert->student_status_after) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4">No certificates issued yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $certificates->links() }}
    </div>

    {{-- Issue New Certificate Section --}}
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-bold mb-4">Issue Transfer Certificate</h2>

        {{-- Step 1: Select Class, Section, Student --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block font-medium">Class</label>
                <select id="class_id" class="w-full border rounded px-3 py-2">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-medium">Section</label>
                <select id="section" class="w-full border rounded px-3 py-2" disabled>
                    <option value="">Select Class First</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Student</label>
                <select id="student_id" class="w-full border rounded px-3 py-2" disabled>
                    <option value="">Select Section First</option>
                </select>
            </div>
        </div>

        {{-- Step 2: Student Information Preview --}}
        <div id="studentPreview" style="display:none;" class="border p-4 rounded mb-6">
            <h3 class="font-bold text-lg mb-2">Student Information</h3>
            <div id="studentDetails"></div>
            <div id="feeSummary"></div>
            <div id="examRecords"></div>
        </div>

        {{-- Step 3: Issue Certificate Form --}}
        <form id="certificateForm" method="POST" action="{{ route('admin.transfer-certificates.store') }}" style="display:none;">
            @csrf
            <input type="hidden" name="student_id" id="form_student_id">

            <div class="mb-4">
                <label class="block font-medium">Certificates to Issue</label>
                <div class="space-y-2 mt-1">
                    <label><input type="checkbox" name="certificate_types[]" value="Academic Excellence Award"> Academic Excellence Award</label><br>
                    <label><input type="checkbox" name="certificate_types[]" value="Sports Achievement Certificate"> Sports Achievement Certificate</label><br>
                    <label><input type="checkbox" name="certificate_types[]" value="Perfect Attendance Award"> Perfect Attendance Award</label><br>
                    <label><input type="checkbox" name="certificate_types[]" value="Good Conduct Certificate"> Good Conduct Certificate</label><br>
                    <label><input type="checkbox" name="certificate_types[]" value="Transfer Certificate"> Transfer Certificate (TC)</label>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Student Status After Transfer</label>
                <select name="student_status_after" class="w-full border rounded px-3 py-2">
                    <option value="active">Keep Active</option>
                    <option value="inactive">Make Inactive</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Remarks (Optional)</label>
                <textarea name="remarks" rows="3" class="w-full border rounded px-3 py-2" placeholder="Enter any additional remarks..."></textarea>
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Issue Certificate(s)</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#class_id').change(function() {
            let classId = $(this).val();
            if (classId) {
                $.get(`/admin/sections/${classId}`, function(data) {
                    let sectionSelect = $('#section');
                    sectionSelect.empty().append('<option value="">Select Section</option>');
                    $.each(data, function(i, section) {
                        sectionSelect.append(`<option value="${section}">${section}</option>`);
                    });
                    sectionSelect.prop('disabled', false);
                    $('#student_id').prop('disabled', true).empty().append('<option value="">Select Section First</option>');
                    $('#studentPreview').hide();
                    $('#certificateForm').hide();
                });
            } else {
                $('#section').prop('disabled', true).empty().append('<option value="">Select Class First</option>');
                $('#student_id').prop('disabled', true).empty().append('<option value="">Select Section First</option>');
            }
        });

        $('#section').change(function() {
            let classId = $('#class_id').val();
            let section = $(this).val();
            if (classId && section) {
                $.get(`/admin/students/${classId}/${section}`, function(data) {
                    let studentSelect = $('#student_id');
                    studentSelect.empty().append('<option value="">Select Student</option>');
                    $.each(data, function(i, student) {
                        studentSelect.append(`<option value="${student.id}">${student.first_name} ${student.last_name} (${student.admission_number})</option>`);
                    });
                    studentSelect.prop('disabled', false);
                });
            }
        });

        $('#student_id').change(function() {
            let studentId = $(this).val();
            if (studentId) {
                $.get(`/admin/student-data/${studentId}`, function(data) {
                    $('#studentPreview').show();
                    $('#form_student_id').val(studentId);
                    
                    let studentHtml = `<div class="grid grid-cols-2 gap-2 mb-3"><strong>Name:</strong> ${data.student.full_name}<br><strong>Admission No:</strong> ${data.student.admission_number}<br><strong>Roll Number:</strong> ${data.student.roll_number}<br><strong>Class:</strong> ${data.student.class.full_name}<br><strong>Section:</strong> ${data.student.section}<br><strong>Father's Name:</strong> ${data.student.father_name}<br><strong>Phone:</strong> ${data.student.phone}<br><strong>Email:</strong> ${data.student.email}</div>`;
                    $('#studentDetails').html(studentHtml);
                    
                    let feeHtml = `<h4 class="font-bold mt-3">Fee Summary</h4><table class="min-w-full"><tr><th>Total</th><td>₹${data.fee_summary.total_amount}</td></tr><tr><th>Paid</th><td>₹${data.fee_summary.total_paid}</td></tr><tr><th>Due</th><td>₹${data.fee_summary.total_due}</td></tr></table>`;
                    $('#feeSummary').html(feeHtml);
                    
                    let examHtml = `<h4 class="font-bold mt-3">Exam Records</h4><p class="text-gray-500">No exam records found.</p>`; // customize later
                    $('#examRecords').html(examHtml);
                    
                    $('#certificateForm').show();
                });
            } else {
                $('#studentPreview').hide();
                $('#certificateForm').hide();
            }
        });
    });
</script>
@endsection