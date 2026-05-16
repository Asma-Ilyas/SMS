@extends('layouts.app')

@section('title', 'Edit Stream')

@section('content')
    <x-form.card title="Edit Stream: {{ $stream->name }}">
        <form method="POST" action="{{ route('admin.streams.update', $stream) }}">
            @csrf @method('PUT')
            <x-form.input name="name" label="Stream Name" :value="$stream->name" required />
            <div class="flex justify-end space-x-2">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Update Stream</x-form.button>
            </div>
        </form>
    </x-form.card>
@endsection