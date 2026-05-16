@extends('layouts.app')
@section('title', 'Record Fee Payment')
@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Record New Fee Payment</h2>

        <form method="POST" action="{{ route('admin.fee-submissions.store') }}" id="paymentForm">
            @csrf

            <div class="space-y-4">
                {{-- Class --}}
                <div>
                    <label class="block font-medium">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Section --}}
                <div>
                    <label class="block font-medium">Section</label>
                    <select name="section" id="section" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Section</option>
                    </select>
                    @error('section')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Student with reliable search & select --}}
                <div>
                    <label class="block font-medium">Student <span class="text-red-500">*</span></label>
                    
                    <!-- Hidden field stores selected student ID -->
                    <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}">
                    
                    <!-- Search input -->
                    <input type="text" id="student_search"
                           class="w-full border rounded px-3 py-2"
                           placeholder="Type student name to search..."
                           autocomplete="off"
                           disabled>
                    
                    <!-- Dropdown list of students (appears below search box) -->
                    <div id="student_list" class="hidden border rounded mt-1 max-h-48 overflow-y-auto bg-white shadow-lg">
                        <div class="p-2 text-gray-500 text-sm" id="student_list_loading">Loading students...</div>
                    </div>
                    
                    <!-- Display selected student -->
                    <div id="selected_student_display" class="mt-2 text-sm text-green-700 font-medium hidden">
                        Selected: <span id="selected_student_name"></span>
                        <button type="button" id="clear_student" class="ml-2 text-red-500 underline">Clear</button>
                    </div>
                    
                    <div id="studentError" class="text-red-600 text-sm hidden">Please select a student.</div>
                    @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Fee Types as Checkboxes --}}
                <div>
                    <label class="block font-medium mb-2">Fee Types <span class="text-red-500">*</span></label>
                    <div class="border rounded-lg p-3 max-h-60 overflow-y-auto bg-gray-50">
                        @foreach($feeTypes as $type)
                            <label class="flex items-start mb-2 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                <input type="checkbox" 
                                       name="fee_type_ids[]" 
                                       value="{{ $type->id }}"
                                       data-amount="{{ $type->amount }}"
                                       class="fee-checkbox mt-1 mr-2"
                                       {{ (is_array(old('fee_type_ids')) && in_array($type->id, old('fee_type_ids'))) ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <span class="font-medium">{{ $type->name }}</span>
                                    <span class="text-gray-600 text-sm ml-2">(₹{{ number_format($type->amount, 2) }})</span>
                                    @if($type->description)
                                        <p class="text-xs text-gray-500">{{ $type->description }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Select one or more fee types.</p>
                    <div id="feeError" class="text-red-600 text-sm hidden">Please select at least one fee type.</div>
                    @error('fee_type_ids')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Total Amount --}}
                <div>
                    <label class="block font-medium">Total Amount (₹)</label>
                    <div class="text-2xl font-bold text-green-700 bg-gray-100 p-3 rounded">
                        ₹ <span id="totalAmount">0.00</span>
                    </div>
                </div>

                {{-- Submission Date --}}
                <div>
                    <label class="block font-medium">Submission Date <span class="text-red-500">*</span></label>
                    <input type="date" name="submission_date" value="{{ old('submission_date', date('Y-m-d')) }}" class="w-full border rounded px-3 py-2" required>
                    @error('submission_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Receipt Number --}}
                <div>
                    <label class="block font-medium">Receipt Number (Optional)</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number') }}" class="w-full border rounded px-3 py-2">
                </div>

                {{-- Remarks --}}
                <div>
                    <label class="block font-medium">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full border rounded px-3 py-2">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.fee-submissions.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save Payment(s)</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        // DOM elements
        const classSelect = document.getElementById('class_id');
        const sectionSelect = document.getElementById('section');
        const studentSearch = document.getElementById('student_search');
        const studentListDiv = document.getElementById('student_list');
        const studentIdHidden = document.getElementById('student_id');
        const selectedDisplayDiv = document.getElementById('selected_student_display');
        const selectedStudentNameSpan = document.getElementById('selected_student_name');
        const clearStudentBtn = document.getElementById('clear_student');
        const studentError = document.getElementById('studentError');
        const feeCheckboxes = document.querySelectorAll('.fee-checkbox');
        const totalSpan = document.getElementById('totalAmount');
        const feeError = document.getElementById('feeError');
        const form = document.getElementById('paymentForm');

        let allStudents = [];      // { id, name (cleaned) }
        let currentFilter = '';

        // Helper to clean "SharedNameStudent" prefix
        function cleanStudentName(rawName) {
            let name = rawName;
            name = name.replace(/^SharedNameStudent\s*/i, '');
            name = name.replace(/\s*\([^)]*\)/, '').replace(/\s*[-–]\s*\w+$/, '');
            return name.trim();
        }

        // Render the dropdown list based on filter text
        function renderStudentList(filter = '') {
            if (!allStudents.length) {
                studentListDiv.innerHTML = '<div class="p-2 text-gray-500 text-sm">No students in this section</div>';
                return;
            }
            const filtered = filter.trim() === '' 
                ? allStudents 
                : allStudents.filter(s => s.name.toLowerCase().includes(filter.toLowerCase()));
            
            if (filtered.length === 0) {
                studentListDiv.innerHTML = '<div class="p-2 text-gray-500 text-sm">No matching students</div>';
                return;
            }
            
            const html = filtered.map(student => `
                <div class="student-option px-3 py-2 hover:bg-gray-100 cursor-pointer border-b last:border-0"
                     data-id="${student.id}"
                     data-name="${escapeHtml(student.name)}">
                    ${escapeHtml(student.name)}
                </div>
            `).join('');
            studentListDiv.innerHTML = html;
            
            // Attach click handlers to each option
            document.querySelectorAll('.student-option').forEach(option => {
                option.addEventListener('click', (e) => {
                    const id = option.getAttribute('data-id');
                    const name = option.getAttribute('data-name');
                    selectStudent(id, name);
                });
            });
        }

        // Helper to escape HTML
        function escapeHtml(str) {
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Select a student
        function selectStudent(id, name) {
            studentIdHidden.value = id;
            studentSearch.value = name;        // show selected name in search box
            selectedStudentNameSpan.innerText = name;
            selectedDisplayDiv.classList.remove('hidden');
            studentListDiv.classList.add('hidden');
            studentError.classList.add('hidden');
            if (studentSearch) studentSearch.blur();
        }

        // Clear selection
        function clearStudent() {
            studentIdHidden.value = '';
            studentSearch.value = '';
            selectedDisplayDiv.classList.add('hidden');
            studentError.classList.add('hidden');
            // Optionally re-focus search
            studentSearch.focus();
            renderStudentList(currentFilter);
        }

        // Load students from AJAX
        function loadStudents(classId, section) {
            studentSearch.disabled = true;
            studentSearch.value = '';
            studentIdHidden.value = '';
            selectedDisplayDiv.classList.add('hidden');
            studentListDiv.innerHTML = '<div class="p-2 text-gray-500 text-sm">Loading students...</div>';
            studentListDiv.classList.remove('hidden');
            
            fetch(`/admin/get-students/${classId}/${section}`)
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (!Array.isArray(data)) throw new Error('Invalid data');
                    allStudents = data.map(s => ({
                        id: s.id,
                        name: cleanStudentName(s.name)
                    }));
                    renderStudentList('');
                    studentSearch.disabled = false;
                    studentSearch.placeholder = "Type student name...";
                })
                .catch(err => {
                    console.error('Student fetch error:', err);
                    studentListDiv.innerHTML = '<div class="p-2 text-red-500 text-sm">Error loading students</div>';
                    studentSearch.disabled = true;
                });
        }

        // Class change -> load sections
        classSelect.addEventListener('change', function() {
            const classId = this.value;
            if (!classId) {
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                sectionSelect.disabled = false;
                studentSearch.disabled = true;
                studentSearch.value = '';
                studentListDiv.classList.add('hidden');
                allStudents = [];
                return;
            }
            sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
            sectionSelect.disabled = true;
            studentSearch.disabled = true;
            studentListDiv.classList.add('hidden');
            
            fetch(`/admin/get-sections/${classId}`)
                .then(res => res.ok ? res.json() : Promise.reject())
                .then(data => {
                    sectionSelect.innerHTML = '<option value="">Select Section</option>';
                    if (Array.isArray(data) && data.length) {
                        data.forEach(section => {
                            const opt = document.createElement('option');
                            opt.value = section;
                            opt.textContent = section;
                            sectionSelect.appendChild(opt);
                        });
                        sectionSelect.disabled = false;
                    } else {
                        sectionSelect.innerHTML += '<option value="" disabled>No sections</option>';
                    }
                    // Reset student area
                    studentSearch.disabled = true;
                    studentSearch.value = '';
                    studentListDiv.classList.add('hidden');
                    allStudents = [];
                })
                .catch(() => {
                    sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
                });
        });

        // Section change -> load students
        sectionSelect.addEventListener('change', function() {
            const classId = classSelect.value;
            const section = this.value;
            if (!classId || !section) {
                studentSearch.disabled = true;
                studentSearch.value = '';
                studentListDiv.classList.add('hidden');
                allStudents = [];
                return;
            }
            loadStudents(classId, section);
        });

        // Search input: filter students as user types
        studentSearch.addEventListener('input', function(e) {
            if (!allStudents.length) return;
            currentFilter = e.target.value;
            renderStudentList(currentFilter);
            studentListDiv.classList.remove('hidden');
        });

        // Show/hide dropdown on focus/blur
        studentSearch.addEventListener('focus', function() {
            if (allStudents.length) {
                renderStudentList(currentFilter);
                studentListDiv.classList.remove('hidden');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!studentSearch.contains(e.target) && !studentListDiv.contains(e.target)) {
                studentListDiv.classList.add('hidden');
            }
        });

        clearStudentBtn.addEventListener('click', clearStudent);

        // Total calculation
        function updateTotal() {
            let total = 0;
            feeCheckboxes.forEach(cb => {
                if (cb.checked) total += parseFloat(cb.dataset.amount) || 0;
            });
            totalSpan.innerText = total.toFixed(2);
        }
        feeCheckboxes.forEach(cb => cb.addEventListener('change', function() {
            updateTotal();
            if (Array.from(feeCheckboxes).some(c => c.checked)) feeError.classList.add('hidden');
        }));
        updateTotal();

        // Validation before submit
        form.addEventListener('submit', function(e) {
            let valid = true;
            if (!studentIdHidden.value) {
                studentError.classList.remove('hidden');
                valid = false;
            } else {
                studentError.classList.add('hidden');
            }
            if (!Array.from(feeCheckboxes).some(cb => cb.checked)) {
                feeError.classList.remove('hidden');
                valid = false;
            } else {
                feeError.classList.add('hidden');
            }
            if (!valid) e.preventDefault();
        });

        // If class and section are pre-selected (e.g., after validation error), trigger loading
        if (classSelect.value && sectionSelect.value) {
            loadStudents(classSelect.value, sectionSelect.value);
        }
        // Also, if there is an old student_id value, we can try to pre-select (but we don't have the name)
        if (studentIdHidden.value) {
            // We could load student name from stored data, but leave as is.
        }
    })();
</script>
@endpush
@endsection