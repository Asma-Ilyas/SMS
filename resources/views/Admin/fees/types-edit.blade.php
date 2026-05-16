@extends('layouts.app')

@section('title', 'Edit Fee Type')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Fee Type</h2>

        <form method="POST" action="{{ route('admin.fee-types.update', $feeType) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block font-medium text-gray-700">Fee Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $feeType->name) }}" class="w-full border rounded px-3 py-2">
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $feeType->amount) }}" class="w-full border rounded px-3 py-2">
                    @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $feeType->description) }}</textarea>
                    @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $feeType->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.fee-types.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Fee Type</button>
            </div>
        </form>
    </div>
</div>
@endsection