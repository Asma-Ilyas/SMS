@extends('layouts.app')
@section('title', 'Edit Slot - ' . $timeSlot->label)

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Edit Slot: {{ $timeSlot->label }}</h1>
        <form method="POST" action="{{ route('admin.slots.update', [$schoolTiming, $timeSlot]) }}" class="bg-white shadow-sm rounded-lg p-6">
            @csrf
            @method('PUT')
            @include('admin.slots.form', ['slot' => $timeSlot])
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('admin.slots.index', $schoolTiming) }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Update Slot</button>
            </div>
        </form>
    </div>
</div>
@endsection