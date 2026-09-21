@extends('layouts.app')
@section('title', 'Request Leave')
@section('content')
<div class="max-w-2xl space-y-6">
    @include('teacher.partials.header', ['title' => 'Request Leave', 'subtitle' => 'Your request will be sent to the administrator for approval'])

    <form method="POST" action="{{ route('teacher.leaves.store') }}" class="bg-white rounded-xl border shadow-sm p-6 space-y-4">
        @csrf
        <div>
            <label class="text-xs text-gray-500">Leave type</label>
            <select name="type" required class="block w-full mt-1 rounded-lg border-gray-300 text-sm">
                @foreach(['sick' => 'Sick leave', 'casual' => 'Casual leave', 'annual' => 'Annual leave'] as $v => $l)
                    <option value="{{ $v }}" @selected(old('type') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-xs text-gray-500">From</label>
                <input type="date" name="start_date" required value="{{ old('start_date') }}" class="block w-full mt-1 rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs text-gray-500">To</label>
                <input type="date" name="end_date" required value="{{ old('end_date') }}" class="block w-full mt-1 rounded-lg border-gray-300 text-sm"></div>
        </div>
        <div>
            <label class="text-xs text-gray-500">Reason</label>
            <textarea name="reason" rows="4" required minlength="5" maxlength="1000" class="block w-full mt-1 rounded-lg border-gray-300 text-sm">{{ old('reason') }}</textarea>
        </div>
        <div class="flex gap-3">
            <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Submit Request</button>
            <a href="{{ route('teacher.leaves.index') }}" class="px-5 py-2 bg-white border rounded-lg text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
