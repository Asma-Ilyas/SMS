@extends('layouts.app')

@section('content')
    <x-form.card title="Create Academic Session">
        <form method="POST" action="{{ route('admin.sessions.store') }}">
            @csrf
            
            <x-form.input name="name" label="Session Name" required />
            
            <div class="grid grid-cols-2 gap-4">
                <x-form.input name="start_date" label="Start Date" type="date" required />
                <x-form.input name="end_date" label="End Date" type="date" required />
            </div>
            
            <x-form.checkbox name="is_active" label="Set as active session" />
            
            <x-form.textarea name="description" label="Description" rows="3" />
            
            <div class="flex justify-end space-x-2">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Save Session</x-form.button>
            </div>
        </form>
    </x-form.card>
@endsection