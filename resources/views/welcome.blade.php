@extends('layouts.app')

@section('content')
    @foreach($sections as $section)
        @php
            $data = collect($section->values)->mapWithKeys(fn($v) => [$v->field->name => $v->value]);
        @endphp
        @includeIf('components.' . $section->type->slug, compact('data', 'section'))
    @endforeach
@endsection