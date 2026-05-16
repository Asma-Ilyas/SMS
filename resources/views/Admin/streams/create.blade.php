@extends('layouts.app')

@section('title', 'Create Stream')

@section('content')
    <x-form.card title="Create Stream">
        <form method="POST" action="{{ route('admin.streams.store') }}">
            @csrf
            <x-form.input name="name" label="Stream Name (e.g., Science, Arts)" required />
            <div class="flex justify-end space-x-2">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Save Stream</x-form.button>
            </div>
        </form>
    </x-form.card>
@endsection