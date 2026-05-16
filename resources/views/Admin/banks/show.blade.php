@extends('layouts.app')

@section('title', 'Bank Details')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Bank Details</h2>
        <div class="border-t border-b py-4 space-y-2">
            <p><strong>Name:</strong> {{ $bank->name }}</p>
            <p><strong>Branch:</strong> {{ $bank->branch_name }}</p>
            <p><strong>Account Title:</strong> {{ $bank->account_title }}</p>
            <p><strong>Account Number:</strong> {{ $bank->account_number }}</p>
            @if($bank->iban)<p><strong>IBAN:</strong> {{ $bank->iban }}</p>@endif
            @if($bank->routing_number)<p><strong>Routing Number:</strong> {{ $bank->routing_number }}</p>@endif
            @if($bank->address)<p><strong>Address:</strong> {{ $bank->address }}</p>@endif
            <p><strong>Status:</strong> {{ $bank->is_active ? 'Active' : 'Inactive' }}</p>
        </div>
        <div class="flex justify-end mt-4">
            <a href="{{ route('admin.banks.edit', $bank) }}" class="px-4 py-2 bg-yellow-600 text-white rounded">Edit</a>
            <a href="{{ route('admin.banks.index') }}" class="px-4 py-2 bg-gray-200 rounded ml-2">Back</a>
        </div>
    </div>
</div>
@endsection