@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Academic Sessions</h1>
        <a href="{{ route('admin.sessions.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ New Session</a>
    </div>

    <x-table.data-table 
        :columns="['No','Name', 'Start Date', 'End Date', 'Active']"
        :rows="$sessions->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'start_date' => $s->start_date->format('Y-m-d'),
            'end_date' => $s->end_date->format('Y-m-d'),
            'active' => $s->is_active ? '✅ Active' : '❌ Inactive',
        ])"
        editRoute="admin.sessions.edit"
        deleteRoute="admin.sessions.destroy"
         routeParameterName="session"
    />
    
    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
@endsection