@extends('layouts.app')

@section('title', 'Add Bank')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Add New Bank Account</h2>
        <form method="POST" action="{{ route('admin.banks.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block font-medium">Bank Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">Branch Name <span class="text-red-500">*</span></label>
                    <input type="text" name="branch_name" value="{{ old('branch_name') }}" class="w-full border rounded px-3 py-2" required>
                    @error('branch_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">Account Title <span class="text-red-500">*</span></label>
                    <input type="text" name="account_title" value="{{ old('account_title') }}" class="w-full border rounded px-3 py-2" required>
                    @error('account_title')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">Account Number <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" class="w-full border rounded px-3 py-2" required>
                    @error('account_number')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block font-medium">IBAN (Optional)</label>
                    <input type="text" name="iban" value="{{ old('iban') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Routing Number (Optional)</label>
                    <input type="text" name="routing_number" value="{{ old('routing_number') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Bank Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <span class="ml-2">Active (show in invoice dropdown)</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('admin.banks.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save Bank</button>
            </div>
        </form>
    </div>
</div>
@endsection