@extends('layouts.app')

@section('title', 'Create Transfer Certificate')

@section('content')
<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📜 Create Transfer Certificate</h1>
                <p class="text-sm text-gray-500 mt-1">Issue a new transfer certificate for a student</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.transfer-certificates.index') }}" 
                   class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    ← Back
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.transfer-certificates.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Student Selection --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Student <span class="text-red-500">*</span></label>
                    <select name="student_id" id="student_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} 
                                ({{ $student->admission_number ?? 'N/A' }})
                                @if($student->classSection)
                                    - {{ optional($student->classSection->class)->grade->name ?? '' }} {{ $student->classSection->section_name ?? '' }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Student Details (Auto-populated) --}}
                <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 mb-2">
                    <h4 class="font-semibold text-gray-700 mb-2">Student Details</h4>
                    <div id="studentDetails" class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Name</p>
                            <p class="font-medium text-gray-900" id="studentName">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Admission No</p>
                            <p class="font-medium text-gray-900" id="studentAdmission">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Roll No</p>
                            <p class="font-medium text-gray-900" id="studentRoll">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Section</p>
                            <p class="font-medium text-gray-900" id="studentSection">-</p>
                        </div>
                    </div>
                </div>

                {{-- Certificate Types --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Types <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @php
                            $certificateTypes = ['Character', 'Academic', 'Sports', 'Conduct', 'Attendance', 'Transfer'];
                        @endphp
                        @foreach($certificateTypes as $type)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="certificate_types[]" value="{{ $type }}" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       {{ in_array($type, old('certificate_types', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $type }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('certificate_types')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('certificate_types.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Student Status After --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Status After <span class="text-red-500">*</span></label>
                    <select name="student_status_after" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="active" {{ old('student_status_after') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('student_status_after') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('student_status_after')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Issued Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issued Date <span class="text-red-500">*</span></label>
                    <input type="date" name="issued_date" value="{{ old('issued_date', date('Y-m-d')) }}" 
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('issued_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.transfer-certificates.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                    💾 Create Certificate
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-populate student details on selection
        $('#student_id').on('change', function() {
            var studentId = $(this).val();
            
            if (studentId) {
                $.ajax({
                    url: '/admin/transfer-certificates/get-student-data/' + studentId,
                    type: 'GET',
                    success: function(data) {
                        if (data.success) {
                            var student = data.student;
                            $('#studentName').text(student.name || '-');
                            $('#studentAdmission').text(student.admission_number || '-');
                            $('#studentRoll').text(student.roll_number || '-');
                            $('#studentSection').text(student.section || '-');
                        }
                    },
                    error: function() {
                        $('#studentName').text('Error loading data');
                    }
                });
            } else {
                $('#studentName').text('-');
                $('#studentAdmission').text('-');
                $('#studentRoll').text('-');
                $('#studentSection').text('-');
            }
        });
    });
</script>
@endpush