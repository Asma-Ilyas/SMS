@extends('layouts.app')

@section('title', 'Transfer In Student')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Transfer In – New Student from Other School</h2>
        <form method="POST" action="{{ route('admin.transfers.incoming.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label>First Name *</label><input type="text" name="first_name" required class="w-full border rounded px-3 py-2"></div>
                <div><label>Last Name *</label><input type="text" name="last_name" required class="w-full border rounded px-3 py-2"></div>
                <div><label>Gender *</label><select name="gender" class="w-full border rounded px-3 py-2"><option>Male</option><option>Female</option></select></div>
                <div><label>Date of Birth *</label><input type="date" name="date_of_birth" required class="w-full border rounded px-3 py-2"></div>
                <div><label>Admission Number *</label><input type="text" name="admission_number" required class="w-full border rounded px-3 py-2"></div>
                <div><label>Class *</label><select name="class_id" class="w-full border rounded px-3 py-2">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->full_name }}</option>@endforeach</select></div>
                <div><label>Section *</label><input type="text" name="section" required class="w-full border rounded px-3 py-2"></div>
                <div><label>Father's Name *</label><input type="text" name="father_name" required class="w-full border rounded px-3 py-2"></div>
                <div><label>From School *</label><input type="text" name="from_school" required class="w-full border rounded px-3 py-2"></div>
                <div><label>Transfer Date *</label><input type="date" name="transfer_date" required class="w-full border rounded px-3 py-2"></div>
                <div class="col-span-full"><label>Reason</label><textarea name="reason" rows="2" class="w-full border rounded px-3 py-2"></textarea></div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Transfer In & Admit</button>
            </div>
        </form>
    </div>
</div>
@endsection