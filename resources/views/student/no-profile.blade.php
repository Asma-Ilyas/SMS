@extends('layouts.app')
@section('title', 'Student Profile Not Linked')
@section('content')
<div class="max-w-xl mx-auto mt-16 bg-white rounded-2xl shadow p-8 text-center">
    <div class="w-16 h-16 mx-auto rounded-full bg-yellow-100 flex items-center justify-center text-3xl mb-4">⚠️</div>
    <h1 class="text-xl font-bold text-gray-800 mb-2">Student record not linked</h1>
    <p class="text-gray-600 text-sm">
        Your login account ({{ auth()->user()->email }}) is not linked to any student record.
        Please ask the school office to link your account to your student profile.
    </p>
</div>
@endsection
