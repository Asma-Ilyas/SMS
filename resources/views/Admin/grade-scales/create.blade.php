@extends('layouts.app')
@section('title', 'Add Grade Scale')
@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Add Grade Scale</h1>
        <form method="POST" action="{{ route('admin.grade-scales.store') }}">
            @csrf
            <div class="mb-4">
                <label>Name</label>
                <input type="text" name="name" required class="w-full border rounded p-2">
            </div>
            <div id="grades-container">
                <label>Grade Ranges (min, max, grade)</label>
                <div class="grade-row grid grid-cols-3 gap-2 mb-2">
                    <input type="number" name="grades[0][min]" placeholder="Min" required>
                    <input type="number" name="grades[0][max]" placeholder="Max" required>
                    <input type="text" name="grades[0][grade]" placeholder="Grade" required>
                </div>
                <div class="grade-row grid grid-cols-3 gap-2 mb-2">
                    <input type="number" name="grades[1][min]" placeholder="Min" required>
                    <input type="number" name="grades[1][max]" placeholder="Max" required>
                    <input type="text" name="grades[1][grade]" placeholder="Grade" required>
                </div>
                <!-- You can add more rows via JS -->
            </div>
            <div class="mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_default" value="1"> <span class="ml-2">Set as default</span>
                </label>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection