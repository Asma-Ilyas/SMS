@extends('layouts.app')

@section('title', 'Edit Room')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">Rooms</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Edit Room: {{ $room->name }}</h4>
        <p class="text-muted small mb-0">Update room details. Capacity changes affect future assignments.</p>
    </div>
    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Please fix:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('admin.rooms.update', $room) }}">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Basic Info --}}
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-door-open me-2 text-primary"></i>Room Details
                </div>
                <div class="card-body row g-3">
                    <div class="col-sm-8">
                        <label class="form-label">Room Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $room->name) }}"
                               placeholder="e.g. Computer Lab 1, Main Hall">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number"
                               class="form-control @error('room_number') is-invalid @enderror"
                               value="{{ old('room_number', $room->room_number) }}"
                               placeholder="e.g. A-101">
                        @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Block and Floor fields (same as create) --}}
                    <div class="col-sm-6">
                        <label class="form-label">Block (text)</label>
                        <input type="text" name="block"
                               class="form-control @error('block') is-invalid @enderror"
                               value="{{ old('block', $room->block) }}"
                               placeholder="e.g. Block A, Science Wing">
                        @error('block')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Block (reference)</label>
                        <select name="block_id" class="form-select @error('block_id') is-invalid @enderror">
                            <option value="">-- Select Block --</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id', $room->block_id) == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }} ({{ $block->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('block_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-3">
                        <label class="form-label">Floor (number) <span class="text-danger">*</span></label>
                        <select name="floor" class="form-select @error('floor') is-invalid @enderror">
                            @for($f = 0; $f <= 10; $f++)
                                <option value="{{ $f }}" {{ old('floor', $room->floor) == $f ? 'selected':'' }}>
                                    {{ $f === 0 ? 'Ground Floor' : 'Floor '.$f }}
                                </option>
                            @endfor
                        </select>
                        @error('floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Floor (reference)</label>
                        <select name="floor_id" class="form-select @error('floor_id') is-invalid @enderror">
                            <option value="">-- Select Floor --</option>
                            @foreach($floors as $floor)
                                <option value="{{ $floor->id }}" {{ old('floor_id', $room->floor_id) == $floor->id ? 'selected' : '' }}>
                                    {{ $floor->name }} (Level {{ $floor->level }})
                                </option>
                            @endforeach
                        </select>
                        @error('floor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            @foreach(['classroom','lab','library','auditorium','activity','other'] as $t)
                                <option value="{{ $t }}" {{ old('type', $room->type) === $t ? 'selected':'' }}>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Capacity card (same as create) --}}
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-people me-2 text-success"></i>Capacity
                </div>
                <div class="card-body row g-3">
                    <div class="col-sm-4">
                        <label class="form-label">Max Students <span class="text-danger">*</span></label>
                        <input type="number" name="capacity"
                               class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', $room->capacity) }}"
                               min="1" max="2000" placeholder="e.g. 40">
                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Used for auto-assignment fit checks.</div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info py-2 mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Fit Labels:</strong>
                            <span class="badge bg-success ms-1">Perfect</span> capacity ≥ strength, ≤10 spare &nbsp;
                            <span class="badge bg-primary">Comfortable</span> plenty of spare seats &nbsp;
                            <span class="badge bg-warning text-dark">Tight</span> ≤3 spare seats &nbsp;
                            <span class="badge bg-danger">Over Capacity</span> room too small
                        </div>
                    </div>
                </div>
            </div>

            {{-- Features card (same as create) --}}
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-stars me-2 text-warning"></i>Features
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input type="hidden" name="has_projector" value="0">
                                <input type="checkbox" class="form-check-input" id="hasProjector"
                                       name="has_projector" value="1"
                                       {{ old('has_projector', $room->has_projector) ? 'checked':'' }}>
                                <label class="form-check-label" for="hasProjector">
                                    <i class="bi bi-projector me-1"></i> Has Projector
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check form-switch">
                                <input type="hidden" name="has_ac" value="0">
                                <input type="checkbox" class="form-check-input" id="hasAc"
                                       name="has_ac" value="1"
                                       {{ old('has_ac', $room->has_ac) ? 'checked':'' }}>
                                <label class="form-check-label" for="hasAc">
                                    <i class="bi bi-snow me-1"></i> Has AC
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="2" placeholder="Any special notes about this room…">{{ old('notes', $room->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right sidebar (availability & buttons) --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-gear me-2"></i>Availability
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" class="form-check-input" id="isAvailable"
                               name="is_available" value="1"
                               {{ old('is_available', $room->is_available) ? 'checked':'' }}>
                        <label class="form-check-label" for="isAvailable">
                            <span class="fw-semibold">Room is Available</span><br>
                            <small class="text-muted">Unavailable rooms are excluded from auto‑assignment.</small>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Optional: Show current assignment count --}}
            @if($room->assignments()->count() > 0)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-2"></i>Active Assignments
                    </div>
                    <div class="card-body small">
                        This room is currently assigned to
                        <strong>{{ $room->assignments()->count() }}</strong> section(s).
                        Changing capacity may affect fit status.
                    </div>
                </div>
            @endif

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Room
                </button>
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection