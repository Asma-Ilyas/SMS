@extends('layouts.app')

@section('title', 'Grades')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Grades</h1>
        <a href="{{ route('admin.grades.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ New Grade</a>
    </div>

    <x-table.data-table 
        :columns="['No','Name', 'Numeric Value']"
        :rows="$grades->map(fn($g) => [
            'id' => $g->id,
            'name' => $g->name,
            'numeric_value' => $g->numeric_value,
        ])"
        editRoute="admin.grades.edit"
        deleteRoute="admin.grades.destroy"
        routeParameterName="grade"
    />

    <div class="mt-4">{{ $grades->links() }}</div>
@endsection