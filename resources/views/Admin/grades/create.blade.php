@extends('layouts.app')

@section('title', 'Create Grade')

@section('content')
    <x-form.card title="Create Grade">
        <form method="POST" action="{{ route('admin.grades.store') }}">
            @csrf
            <x-form.input name="name" label="Grade Name (e.g., 9th, 10th)" required />
            <x-form.input name="numeric_value" label="Numeric Value (e.g., 9, 10)" type="number" required />
            <div class="flex justify-end space-x-2">
                <x-form.button variant="secondary" type="button" onclick="window.history.back()">Cancel</x-form.button>
                <x-form.button variant="primary">Save Grade</x-form.button>
            </div>
        </form>
    </x-form.card>
@endsection