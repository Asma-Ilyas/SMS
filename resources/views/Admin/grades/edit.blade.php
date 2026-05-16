@extends('layouts.app')

@section('title', 'Edit Grade')

@section('content')
    <x-form.card title="Edit Grade: {{ $grade->name }}">
        <form method="POST" action="{{ route('admin.grades.update', $grade) }}">
            @csrf @method('PUT')
            <x-form.input name="name" label="Grade Name" :value="$grade->name" required />
            <x-form.input name="numeric_value" label="Numeric Value" type="number" :value="$grade->numeric_value" required />
            <div class="flex justify-end space-x-2">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Update Grade</x-form.button>
            </div>
        </form>
    </x-form.card>
@endsection