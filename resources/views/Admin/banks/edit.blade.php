@extends('layouts.app')

@section('title', 'Edit Bank')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Edit Bank: {{ $bank->name }}</h2>
        <form method="POST" action="{{ route('admin.banks.update', $bank) }}">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block font-medium">Bank Name</label>
                    <input type="text" name="name" value="{{ old('name', $bank->name) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Branch Name</label>
                    <input type="text" name="branch_name" value="{{ old('branch_name', $bank->branch_name) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Account Title</label>
                    <input type="text" name="account_title" value="{{ old('account_title', $bank->account_title) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $bank->account_number) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">IBAN</label>
                    <input type="text" name="iban" value="{{ old('iban', $bank->iban) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Routing Number</label>
                    <input type="text" name="routing_number" value="{{ old('routing_number', $bank->routing_number) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address', $bank->address) }}</textarea>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bank->is_active) ? 'checked' : '' }}>
                        <span class="ml-2">Active</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('admin.banks.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Bank</button>
            </div>
        </form>
    </div>
</div>
@endsection