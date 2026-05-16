@extends('layouts.app')

@section('content')
    <x-form.card title="Edit Session: {{ $session->name }}">
        <form method="POST" action="{{ route('admin.sessions.update', $session) }}">
            @csrf @method('PUT')
            
            <x-form.input name="name" label="Session Name" :value="$session->name" required />
            
            <div class="grid grid-cols-2 gap-4">
                <x-form.input name="start_date" label="Start Date" type="date" :value="$session->start_date->format('Y-m-d')" required />
                <x-form.input name="end_date" label="End Date" type="date" :value="$session->end_date->format('Y-m-d')" required />
            </div>
            
            <x-form.checkbox name="is_active" label="Set as active session" :checked="$session->is_active" />
            
            <x-form.textarea name="description" label="Description" :value="$session->description" rows="3" />
            
            <div class="flex justify-end space-x-2">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Update Session</x-form.button>
            </div>
        </form>
    </x-form.card>
@endsection