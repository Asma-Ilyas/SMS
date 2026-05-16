@extends('layouts.app')

@section('title', 'Discounts')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Discounts</h1>
        <a href="{{ route('admin.discounts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Discount</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y">
            <thead><tr><th>Name</th><th>Type</th><th>Value</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($discounts as $discount)
                <tr>
                    <td>{{ $discount->name }}</td>
                    <td>{{ ucfirst($discount->type) }}</td>
                    <td>{{ $discount->type == 'percentage' ? $discount->value.'%' : '₹'.number_format($discount->value,2) }}</td>
                    <td>{{ $discount->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.discounts.edit', $discount) }}">Edit</a>
                        <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $discounts->links() }}
</div>
@endsection