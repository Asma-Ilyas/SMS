@extends('layouts.app')

@section('title', 'Create Installment Plan')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Create Installment Plan (2–12 Installments)</h2>

        <form method="POST" action="{{ route('admin.fee-installments.store') }}">
            @csrf

            <div class="space-y-4">
                {{-- Student --}}
                <div>
                    <label class="block font-medium text-gray-700">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Fee Type --}}
                <div>
                    <label class="block font-medium text-gray-700">Fee Type <span class="text-red-500">*</span></label>
                    <select name="fee_submission_type_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Fee Type</option>
                        @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}" {{ old('fee_submission_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} - {{ ucfirst($type->period) }} - ₹{{ number_format($type->amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('fee_submission_type_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Total Amount --}}
                <div>
                    <label class="block font-medium text-gray-700">Total Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="total_amount" value="{{ old('total_amount') }}" class="w-full border rounded px-3 py-2" required>
                    @error('total_amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Number of Installments --}}
                <div>
                    <label class="block font-medium text-gray-700">Number of Installments (2–12) <span class="text-red-500">*</span></label>
                    <input type="number" name="installment_count" id="installment_count" min="2" max="12" value="{{ old('installment_count') }}" class="w-full border rounded px-3 py-2" required>
                    @error('installment_count')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Dynamic Due Dates Container --}}
                <div id="due_dates_container">
                    @if(old('due_dates'))
                        @foreach(old('due_dates') as $index => $dueDate)
                            <div class="mt-2">
                                <label class="block font-medium text-gray-700">Due Date for Installment {{ $index + 1 }}</label>
                                <input type="date" name="due_dates[]" value="{{ $dueDate }}" class="w-full border rounded px-3 py-2" required>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Bank (optional, for future invoice generation) --}}
                <div>
                    <label class="block font-medium text-gray-700">Default Bank (for challan)</label>
                    <select name="bank_id" class="w-full border rounded px-3 py-2">
                        <option value="">Select Bank (Optional)</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                {{ $bank->name }} - {{ $bank->account_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <a href="{{ route('admin.fee-installments.index') }}" class="px-4 py-2 bg-gray-200 rounded mr-2">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Create Installments</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('installment_count').addEventListener('change', function() {
        let count = parseInt(this.value);
        if (isNaN(count) || count < 2) count = 2;
        let container = document.getElementById('due_dates_container');
        // Preserve any previously filled? We'll clear and rebuild.
        container.innerHTML = '';
        for (let i = 1; i <= count; i++) {
            let div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="block font-medium text-gray-700">Due Date for Installment ${i}</label>
                <input type="date" name="due_dates[]" class="w-full border rounded px-3 py-2" required>
            `;
            container.appendChild(div);
        }
    });
    // Trigger on load if number already set
    if (document.getElementById('installment_count').value) {
        document.getElementById('installment_count').dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection