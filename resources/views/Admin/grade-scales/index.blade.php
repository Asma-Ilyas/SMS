@extends('layouts.app')
@section('title', 'Grade Scales')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between mb-4">
        <h1 class="text-2xl font-bold">Grade Scales</h1>
        <a href="{{ route('admin.grade-scales.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Scale</a>
    </div>
    <div class="grid gap-4">
        @foreach($scales as $scale)
            <div class="border p-4 rounded">
                <div class="flex justify-between items-center">
                    <div>
                        <strong>{{ $scale->name }}</strong> @if($scale->is_default) <span class="text-green-600 text-sm">(Default)</span> @endif
                    </div>
                    <div class="space-x-2">
                        <a href="{{ route('admin.grade-scales.edit', $scale) }}" class="text-blue-600">Edit</a>
                        <form action="{{ route('admin.grade-scales.destroy', $scale) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </div>
                </div>
                <pre class="text-sm mt-2">{{ json_encode($scale->grades, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endforeach
    </div>
</div>
@endsection