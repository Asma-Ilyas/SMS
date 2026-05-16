@extends('layouts.app')

@section('title', 'Streams')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Streams</h1>
        <a href="{{ route('admin.streams.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ New Stream</a>
    </div>

    <x-table.data-table 
        :columns="['No','Name']"
        :rows="$streams->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
        ])"
        editRoute="admin.streams.edit"
        deleteRoute="admin.streams.destroy"
        routeParameterName="stream"
    />

    <div class="mt-4">{{ $streams->links() }}</div>
@endsection