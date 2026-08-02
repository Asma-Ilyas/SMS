{{-- resources/views/admin/rooms/show.blade.php --}}
@extends('layouts.app')
@section('title', $room->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">Rooms</a></li>
    <li class="breadcrumb-item active">{{ $room->room_number }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">{{ $room->name }}
            <span class="text-muted fw-normal small ms-1">{{ $room->room_number }}</span>
        </h4>
        <p class="text-muted small mb-0">{{ $room->block ?? '' }} · {{ $room->floor_label }} · {{ ucfirst($room->type) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <form action="{{ route('admin.rooms.toggle', $room) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-sm {{ $room->is_available ? 'btn-outline-warning' : 'btn-outline-success' }}">
                <i class="bi bi-{{ $room->is_available ? 'eye-slash' : 'eye' }} me-1"></i>
                {{ $room->is_available ? 'Mark Unavailable' : 'Mark Available' }}
            </button>
        </form>
    </div>
</div>

<div class="row g-4">

    {{-- Info Cards --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header fw-semibold">Room Info</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Number</td><td class="fw-semibold">{{ $room->room_number }}</td></tr>
                    <tr><td class="text-muted">Block</td><td>{{ $room->block ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Floor</td><td>{{ $room->floor_label }}</td></tr>
                    <tr><td class="text-muted">Type</td><td><span class="badge bg-secondary">{{ ucfirst($room->type) }}</span></td></tr>
                    <tr>
                        <td class="text-muted">Capacity</td>
                        <td><span class="fw-bold fs-5 text-primary">{{ $room->capacity }}</span> students</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Projector</td>
                        <td>@if($room->has_projector)<span class="text-success">✓ Yes</span>@else<span class="text-muted">No</span>@endif</td>
                    </tr>
                    <tr>
                        <td class="text-muted">AC</td>
                        <td>@if($room->has_ac)<span class="text-success">✓ Yes</span>@else<span class="text-muted">No</span>@endif</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($room->is_available)
                                <span class="badge bg-success">Available</span>
                            @else
                                <span class="badge bg-danger">Unavailable</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if($room->notes)
                    <hr>
                    <p class="small text-muted mb-0">{{ $room->notes }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Assignment History --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold d-flex justify-content-between">
                <span>Assignment History</span>
                <span class="badge bg-secondary">{{ $assignments->count() }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Session</th>
                                <th>Section</th>
                                <th>Strength</th>
                                <th>Fit</th>
                                <th>Type</th>
                                <th>Suggestion</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($assignments as $a)
                            <tr>
                                <td class="small">{{ $a->schoolTiming->session_name ?? '—' }}</td>
                                <td class="fw-semibold">
                                    {{ $a->section->name ?? '—' }}
                                    <div class="text-muted small">{{ $a->section->class->name ?? '' }}</div>
                                </td>
                                <td class="text-center">{{ $a->section_strength }}</td>
                                <td>
                                    <span class="badge {{ $a->fit_badge_class }}">{{ $a->fit_label }}</span>
                                    <div class="text-muted" style="font-size:.72rem">
                                        {{ $a->spare_seats >= 0 ? '+' : '' }}{{ $a->spare_seats }} seats
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $a->assignment_type === 'manual' ? 'bg-info text-dark' : 'bg-secondary' }}">
                                        {{ ucfirst($a->assignment_type) }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $a->suggestedRoom ? $a->suggestedRoom->full_name : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No assignment history yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection