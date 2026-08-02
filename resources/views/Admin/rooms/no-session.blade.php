{{-- resources/views/admin/rooms/no-session.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'No Active Session')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center py-5 text-center">
    <div style="font-size:4rem;color:#adb5bd"><i class="bi bi-calendar-x"></i></div>
    <h4 class="mt-3 fw-bold">No Active Session Found</h4>
    <p class="text-muted mb-4" style="max-width:380px">
        You need an active timetable session before rooms can be assigned to sections.
    </p>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.timetable.timings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Session
        </a>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-door-closed me-1"></i> Manage Rooms
        </a>
    </div>
</div>
@endsection