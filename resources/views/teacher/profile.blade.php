@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—';
    $row = fn($l, $v) => '<div><dt class="text-xs uppercase text-gray-500">'.e($l).'</dt><dd class="text-sm text-gray-800 mt-0.5">'.e($v ?: '—').'</dd></div>';
@endphp
<div class="space-y-6 max-w-5xl">
    @include('teacher.partials.header', ['title' => 'My Profile', 'subtitle' => 'Your staff record (contact the office to change these details)'])

    <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-5">
        @if($t->profile_photo)
            <img src="{{ asset('storage/'.$t->profile_photo) }}" class="w-24 h-24 rounded-full object-cover" alt="">
        @else
            <div class="w-24 h-24 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($t->first_name,0,1)) }}</div>
        @endif
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $t->first_name }} {{ $t->last_name }}</h2>
            <p class="text-sm text-gray-500">{{ $t->designation ?: 'Teacher' }} · Employee ID {{ $t->employee_id }}</p>
            @include('teacher.partials.badge', ['status' => $t->is_active ? 'active' : 'inactive'])
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Employment</h3>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {!! $row('Category', $t->category_name) !!}
            {!! $row('Department', $t->department) !!}
            {!! $row('Employment Type', ucfirst(str_replace('_',' ',$t->employment_type))) !!}
            {!! $row('Joining Date', $fmt($t->joining_date)) !!}
            {!! $row('Confirmation Date', $fmt($t->confirmation_date)) !!}
            {!! $row('Contract End', $fmt($t->contract_end_date)) !!}
            {!! $row('Qualification', $t->qualification) !!}
            {!! $row('Specialization', $t->specialization) !!}
            {!! $row('Max periods / day', $t->max_periods_per_day) !!}
            {!! $row('Max periods / week', $t->max_periods_per_week) !!}
            {!! $row('Scheduled arrival', $t->cat_arrival ? substr($t->cat_arrival,0,5) : null) !!}
            {!! $row('Scheduled departure', $t->cat_departure ? substr($t->cat_departure,0,5) : null) !!}
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Personal & Contact</h3>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {!! $row('Father Name', $t->father_name) !!}
            {!! $row('Gender', $t->gender) !!}
            {!! $row('Date of Birth', $fmt($t->date_of_birth)) !!}
            {!! $row('CNIC', $t->cnic) !!}
            {!! $row('Email', $t->email) !!}
            {!! $row('Phone', $t->phone) !!}
            {!! $row('Mobile', $t->mobile) !!}
            {!! $row('City', $t->city) !!}
            {!! $row('Present Address', $t->present_address) !!}
            {!! $row('Emergency Contact', trim($t->emergency_contact_name.' '.($t->emergency_contact_relation ? '('.$t->emergency_contact_relation.')' : ''))) !!}
            {!! $row('Emergency Phone', $t->emergency_contact_phone) !!}
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Bank Details</h3>
        <dl class="grid grid-cols-2 md:grid-cols-3 gap-4">
            {!! $row('Bank', $t->bank_name) !!}
            {!! $row('Account #', $t->bank_account_number) !!}
            {!! $row('IBAN', $t->bank_iban) !!}
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Change Password</h3>
        <form method="POST" action="{{ route('teacher.profile.password') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf @method('PUT')
            <div><label class="text-xs text-gray-500">Current password</label>
                <input type="password" name="current_password" required class="w-full mt-1 rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs text-gray-500">New password</label>
                <input type="password" name="password" required minlength="8" class="w-full mt-1 rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs text-gray-500">Confirm new password</label>
                <input type="password" name="password_confirmation" required class="w-full mt-1 rounded-lg border-gray-300 text-sm"></div>
            <div class="md:col-span-3"><button class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Update Password</button></div>
        </form>
    </div>
</div>
@endsection
