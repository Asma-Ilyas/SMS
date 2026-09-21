@extends('layouts.app')
@section('title', 'Distribute Certificate')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Distribute Certificate</h1>
    <p class="text-gray-500 mb-6">{{ $certificateType->title }}</p>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.certificates.distribute.store', $certificateType) }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-md p-6">
        @csrf

        {{-- Step 1: Class & Section --}}
        <div class="mb-4">
            <label class="block font-medium">Class and Section <span class="text-red-500">*</span></label>
            <select id="class_section_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select class and section</option>
                @foreach($classSections as $cs)
                    <option value="{{ $cs->id }}">
                        {{ $cs->full_name ?? trim(($cs->class?->grade?->name ?? '') . ' ' . $cs->section_name) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Step 2: Student --}}
        <div class="mb-4">
            <label class="block font-medium">Student <span class="text-red-500">*</span></label>
            <select id="student_id" name="student_id" class="w-full border rounded px-3 py-2" disabled required>
                <option value="">First select a class and section</option>
            </select>
            @error('student_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div id="student_info" class="hidden mb-4 rounded bg-blue-50 px-4 py-3 text-sm text-blue-900"></div>

        {{-- Issue date --}}
        <div class="mb-4">
            <label class="block font-medium">Issue Date <span class="text-red-500">*</span></label>
            <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- Remarks --}}
        <div class="mb-4">
            <label class="block font-medium">Remarks</label>
            <textarea name="remarks" rows="3" class="w-full border rounded px-3 py-2">{{ old('remarks') }}</textarea>
        </div>

        {{-- File --}}
        <div class="mb-6">
            <label class="block font-medium">Certificate File (optional)</label>
            <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png"
                   class="w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">PDF, JPG or PNG, up to 2 MB.</p>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 rounded border text-gray-700">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Issue Certificate</button>
        </div>
    </form>
</div>

<script>
    // Built from your real route names, so the URLs always match.
    const STUDENTS_URL = "{{ route('admin.certificates.students.by-class-section', ['classSectionId' => '__ID__']) }}";
    const STUDENT_INFO_URL = "{{ route('admin.certificates.student-info', ['studentId' => '__ID__']) }}";

    const sectionSelect = document.getElementById('class_section_id');
    const studentSelect = document.getElementById('student_id');
    const infoBox = document.getElementById('student_info');

    sectionSelect.addEventListener('change', async function () {
        studentSelect.innerHTML = '<option value="">Loading...</option>';
        studentSelect.disabled = true;
        infoBox.classList.add('hidden');

        if (!this.value) {
            studentSelect.innerHTML = '<option value="">First select a class and section</option>';
            return;
        }

        try {
            const url = STUDENTS_URL.replace('__ID__', this.value);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });

            if (!res.ok) {
                studentSelect.innerHTML = '<option value="">Error ' + res.status + ' loading students</option>';
                console.error('Request failed:', res.status, url);
                return;
            }

            const students = await res.json();

            if (!students.length) {
                studentSelect.innerHTML = '<option value="">No students in this section</option>';
                return;
            }

            studentSelect.innerHTML = '<option value="">Select student</option>';
            students.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = (s.name || 'Student #' + s.id) + ' (' + s.admission_number + ')';
                studentSelect.appendChild(opt);
            });
            studentSelect.disabled = false;
        } catch (e) {
            console.error(e);
            studentSelect.innerHTML = '<option value="">Could not load students</option>';
        }
    });

    studentSelect.addEventListener('change', async function () {
        infoBox.classList.add('hidden');
        if (!this.value) return;
        try {
            const res = await fetch(STUDENT_INFO_URL.replace('__ID__', this.value), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const s = await res.json();
            infoBox.textContent = s.name + ' · Admission #' + s.admission_number + ' · ' + s.class + ' (' + s.section + ')';
            infoBox.classList.remove('hidden');
        } catch (e) { /* the info box is optional */ }
    });
</script>
@endsection