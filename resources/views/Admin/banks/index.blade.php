@extends('layouts.app')

@section('title', 'Bank Accounts')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Bank Accounts</h1>
        <a href="{{ route('admin.banks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Bank
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Bank Name</th>
                    <th class="px-6 py-3 text-left">Branch</th>
                    <th class="px-6 py-3 text-left">Account Title</th>
                    <th class="px-6 py-3 text-left">Account #</th>
                    <th class="px-6 py-3 text-left">IBAN</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banks as $bank)
                <tr class="border-t">
                    <td class="px-6 py-3">{{ $bank->name }}</td>
                    <td class="px-6 py-3">{{ $bank->branch_name }}</td>
                    <td class="px-6 py-3">{{ $bank->account_title }}</td>
                    <td class="px-6 py-3">{{ $bank->account_number }}</td>
                    <td class="px-6 py-3">{{ $bank->iban ?? '-' }}</td>
                    <td class="px-6 py-3">
                        @if($bank->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 space-x-2">
                        <a href="{{ route('admin.banks.edit', $bank) }}" class="text-yellow-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.banks.destroy', $bank) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this bank?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No banks added.</td></tr>
                @endforelse
            </tbody>
         </table>
    </div>
    <div class="mt-4">{{ $banks->links() }}</div>
</div>
@endsection