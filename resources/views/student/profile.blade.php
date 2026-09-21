@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
@php
    $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—';
    $row = fn($l, $v) => '<div><dt class="text-xs uppercase text-gray-500">'.e($l).'</dt><dd class="text-sm text-gray-800 mt-0.5">'.e($v ?: '—').'</dd></div>';
@endphp
<div class="space-y-6 max-w-5xl">
    @include('student.partials.header', ['title' => 'My Profile', 'subtitle' => 'Your personal and academic information'])

    <div class="bg-white rounded-xl shadow-sm border p-6 flex items-center gap-5">
        @if($s->profile_photo)
            <img src="{{ asset('storage/'.$s->profile_photo) }}" class="w-24 h-24 rounded-full object-cover" alt="">
        @else
            <div class="w-24 h-24 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-3xl font-bold">{{ strtoupper(substr($s->first_name,0,1)) }}</div>
        @endif
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ trim($s->first_name.' '.$s->middle_name.' '.$s->last_name) }}</h2>
            <p class="text-sm text-gray-500">Admission # {{ $s->admission_number }} @if($s->roll_number) · Roll # {{ $s->roll_number }} @endif</p>
            @include('student.partials.badge', ['status' => $s->status])
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Academic</h3>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {!! $row('Session', $s->session_name) !!}
            {!! $row('Class', $s->grade_name) !!}
            {!! $row('Section', $s->section_name) !!}
            {!! $row('Stream', $s->stream_name) !!}
            {!! $row('Admission Date', $fmt($s->admission_date)) !!}
            {!! $row('Student Type', $s->student_type) !!}
            {!! $row('Previous School', $s->previous_school_name) !!}
            {!! $row('Previous Class', $s->previous_class) !!}
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Personal</h3>
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {!! $row('Gender', $s->gender) !!}
            {!! $row('Date of Birth', $fmt($s->date_of_birth)) !!}
            {!! $row('Blood Group', $s->blood_group) !!}
            {!! $row('Religion', $s->religion) !!}
            {!! $row('Mother Tongue', $s->mother_tongue) !!}
            {!! $row('Birth Place', $s->birth_place) !!}
            {!! $row('Phone', $s->phone) !!}
            {!! $row('Email', $s->email) !!}
            {!! $row('City', $s->city) !!}
            {!! $row('State', $s->state) !!}
            {!! $row('Country', $s->country) !!}
            {!! $row('Address', $s->address) !!}
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Parents / Guardians</h3>
        <dl class="grid grid-cols-2 md:grid-cols-3 gap-4">
            {!! $row('Father Name', $s->father_name) !!}
            {!! $row('Father Phone', $s->father_phone) !!}
            {!! $row('Father Occupation', $s->father_occupation) !!}
            {!! $row('Mother Name', $s->mother_name) !!}
            {!! $row('Mother Phone', $s->mother_phone) !!}
            {!! $row('Mother Occupation', $s->mother_occupation) !!}
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Change Password</h3>
        <form method="POST" action="{{ route('student.profile.password') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
