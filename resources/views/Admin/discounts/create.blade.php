@extends('layouts.app')

@section('title', 'Create Discount')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Create New Discount</h2>

        <form method="POST" action="{{ route('admin.discounts.store') }}">
            @csrf

            <div class="space-y-4">
                {{-- Discount Name --}}
                <div>
                    <label class="block font-medium text-gray-700">Discount Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" placeholder="e.g., Early Bird, Sibling Discount">
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Discount Type --}}
                <div>
                    <label class="block font-medium text-gray-700">Discount Type <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full border rounded px-3 py-2">
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                    @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Discount Value --}}
                <div>
                    <label class="block font-medium text-gray-700">Value <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="value" value="{{ old('value') }}" class="w-full border rounded px-3 py-2" placeholder="e.g., 10 for 10% or 500 for ₹500">
                    <p class="text-xs text-gray-500 mt-1">For percentage: enter number (e.g., 10 = 10%). For fixed: enter rupee amount.</p>
                    @error('value')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block font-medium text-gray-700">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2" placeholder="Why this discount? Any conditions?">{{ old('description') }}</textarea>
                    @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-gray-700">Active (available for assignment)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.discounts.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Discount</button>
            </div>
        </form>
    </div>
</div>
@endsection