@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Generate Challan / Invoice</h2>
        <form method="POST" action="{{ route('admin.invoices.store') }}" id="invoiceForm">
            @csrf

            {{-- Class --}}
            <div class="mb-4">
                <label>Class</label>
                <select id="class_id" name="class_id" class="w-full border rounded" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Student (AJAX) --}}
            <div class="mb-4">
                <label>Student</label>
                <select id="student_id" name="student_id" class="w-full border rounded" required disabled>
                    <option value="">First select a class</option>
                </select>
            </div>

            {{-- Installment (AJAX) --}}
            <div class="mb-4">
                <label>Fee / Installment</label>
                <select id="installment_id" name="student_fee_installment_id" class="w-full border rounded" required disabled>
                    <option value="">First select a student</option>
                </select>
            </div>

            {{-- Banks only --}}
            <div class="mb-4">
                <label>Bank</label>
                <select name="bank_id" class="w-full border rounded" required>
                    <option value="">Select Bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->name }} @if($bank->account_number) ({{ $bank->account_number }}) @endif
                        </option>
                    @endforeach
                </select>
                <small class="text-gray-500">All active banks are shown.</small>
            </div>

            {{-- Extra charges --}}
            <div class="border-t pt-4 mt-4">
                <h3 class="font-semibold mb-2">Additional Charges / Adjustments</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><label>Late Fee (₹)</label><input type="number" name="late_fee" id="late_fee" class="w-full border rounded" step="0.01" value="0"></div>
                    <div><label>Discount (₹)</label><input type="number" name="discount" id="discount" class="w-full border rounded" step="0.01" value="0"></div>
                    <div class="col-span-2">
                        <label>Other Charges</label>
                        <div class="flex gap-2">
                            <input type="text" name="other_charge_desc" placeholder="Description" class="flex-1 border rounded">
                            <input type="number" name="other_charge_amount" placeholder="Amount ₹" class="w-32 border rounded" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Due date --}}
            <div class="mb-4 mt-4"><label>Due Date</label><input type="date" name="due_date" id="due_date" class="w-full border rounded" required></div>

            {{-- Total payable --}}
            <div class="mb-4 p-3 bg-gray-50 rounded">
                <label class="font-bold">Total Payable (₹)</label>
                <input type="text" id="total_amount" name="amount" class="w-full bg-gray-100 font-bold text-lg" readonly>
            </div>

            <button type="submit" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded">Generate Challan</button>
        </form>
    </div>
</div>

<script>
    async function fetchJSON(url) {
        const resp = await fetch(url);
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        return resp.json();
    }

    // Class → Students
    document.getElementById('class_id').addEventListener('change', async function() {
        const classId = this.value;
        const studentSelect = document.getElementById('student_id');
        const installmentSelect = document.getElementById('installment_id');

        if (!classId) {
            studentSelect.disabled = true;
            studentSelect.innerHTML = '<option value="">Select class first</option>';
            installmentSelect.disabled = true;
            installmentSelect.innerHTML = '<option value="">Select student first</option>';
            document.getElementById('due_date').value = '';
            document.getElementById('total_amount').value = '';
            return;
        }

        studentSelect.disabled = false;
        studentSelect.innerHTML = '<option value="">Loading students...</option>';

        try {
            const students = await fetchJSON(`/admin/get-students-by-class/${classId}`);
            studentSelect.innerHTML = '<option value="">Select Student</option>';
            if (students.length === 0) {
                studentSelect.innerHTML = '<option value="">No students in this class</option>';
            } else {
                students.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = `${s.name} (Roll: ${s.roll_number ?? 'N/A'})`;
                    studentSelect.appendChild(opt);
                });
            }
            installmentSelect.disabled = true;
            installmentSelect.innerHTML = '<option value="">Select student first</option>';
            document.getElementById('due_date').value = '';
            document.getElementById('total_amount').value = '';
        } catch (err) {
            console.error(err);
            studentSelect.innerHTML = '<option value="">Error loading students</option>';
        }
    });

    // Student → Installments
    document.getElementById('student_id').addEventListener('change', async function() {
        const studentId = this.value;
        const installmentSelect = document.getElementById('installment_id');
        if (!studentId) {
            installmentSelect.disabled = true;
            installmentSelect.innerHTML = '<option value="">Select student first</option>';
            return;
        }

        installmentSelect.disabled = false;
        installmentSelect.innerHTML = '<option value="">Loading installments...</option>';

        try {
            const installments = await fetchJSON(`/admin/get-installments/${studentId}`);
            installmentSelect.innerHTML = '<option value="">Select Installment</option>';
            if (installments.length === 0) {
                installmentSelect.innerHTML = '<option value="">No pending installments</option>';
            } else {
                installments.forEach(inst => {
                    const opt = document.createElement('option');
                    opt.value = inst.id;
                    opt.textContent = `${inst.fee_type} - Due: ${inst.due_date} - Remaining: ₹${inst.remaining}`;
                    opt.setAttribute('data-due', inst.due_date);
                    opt.setAttribute('data-amount', inst.remaining);
                    installmentSelect.appendChild(opt);
                });
            }
        } catch (err) {
            console.error(err);
            installmentSelect.innerHTML = '<option value="">Error loading installments</option>';
        }
    });

    // Installment → due date & base amount
    document.getElementById('installment_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const dueDate = selected.getAttribute('data-due') || '';
        const baseAmount = parseFloat(selected.getAttribute('data-amount') || 0);
        document.getElementById('due_date').value = dueDate;
        window.baseAmount = baseAmount;
        calculateTotal();
    });

    // Extra charges
    ['late_fee', 'discount', 'other_charge_amount'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', calculateTotal);
    });

    function calculateTotal() {
        let base = window.baseAmount || 0;
        const late = parseFloat(document.getElementById('late_fee')?.value) || 0;
        const disc = parseFloat(document.getElementById('discount')?.value) || 0;
        const other = parseFloat(document.getElementById('other_charge_amount')?.value) || 0;
        let total = base + late + other - disc;
        document.getElementById('total_amount').value = Math.max(0, total).toFixed(2);
    }
</script>
@endsection