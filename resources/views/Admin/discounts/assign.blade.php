@extends('layouts.app')

@section('title', 'Assign Discount to Student')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Gradient header --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-700 px-6 py-5">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-2xl font-bold text-white tracking-tight">Assign Discount to Student</h2>
            </div>
            <p class="text-emerald-100 text-sm mt-1">Apply a discount (concession) to a specific student</p>
        </div>

        <form method="POST" action="{{ route('admin.discount-assignments.store') }}" class="px-6 py-6 space-y-6">
            @csrf

            {{-- Student dropdown --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                <div class="relative">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <select name="student_id" required class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">Select a student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} ({{ $student->admission_number ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('student_id')<span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            {{-- Discount dropdown --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Discount <span class="text-red-500">*</span></label>
                <div class="relative">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <select name="discount_id" id="discount_id" required class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">Select a discount</option>
                        @foreach($discounts as $discount)
                            <option value="{{ $discount->id }}" data-type="{{ $discount->type }}" data-value="{{ $discount->value }}" {{ old('discount_id') == $discount->id ? 'selected' : '' }}>
                                {{ $discount->name }} ({{ $discount->type == 'percentage' ? $discount->value . '%' : '₹' . number_format($discount->value, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="discountPreview" class="mt-2 text-sm text-emerald-600 font-medium"></div>
                @error('discount_id')<span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            {{-- Fee Type (optional) --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Apply to Specific Fee Type <span class="text-gray-400 text-xs font-normal">(optional)</span></label>
                <div class="relative">
                    <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <select name="fee_submission_type_id" class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                        <option value="">All Fee Types (global discount)</option>
                        @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}" {{ old('fee_submission_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('fee_submission_type_id')<span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>

            {{-- Validity dates (2 columns) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Valid From</label>
                    <input type="date" name="valid_from" value="{{ old('valid_from') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    <p class="text-xs text-gray-500 mt-1">Leave empty = active immediately</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Valid Until</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    <p class="text-xs text-gray-500 mt-1">Leave empty = no expiry</p>
                    @error('valid_until')<span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.discount-assignments.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg shadow-md hover:from-emerald-700 hover:to-teal-700 focus:ring-2 focus:ring-emerald-500 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Assign Discount
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const discountSelect = document.getElementById('discount_id');
    const previewDiv = document.getElementById('discountPreview');

    function updatePreview() {
        const selected = discountSelect.options[discountSelect.selectedIndex];
        if (selected && selected.value) {
            const type = selected.dataset.type;
            const value = selected.dataset.value;
            if (type === 'percentage') {
                previewDiv.innerHTML = `✓ This discount reduces fees by <strong>${value}%</strong>`;
            } else {
                previewDiv.innerHTML = `✓ This discount reduces fees by a fixed amount of <strong>₹${parseFloat(value).toFixed(2)}</strong>`;
            }
        } else {
            previewDiv.innerHTML = '';
        }
    }

    discountSelect.addEventListener('change', updatePreview);
    updatePreview();
</script>
@endpush
@endsection