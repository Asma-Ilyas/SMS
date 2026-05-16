@extends('layouts.app')

@section('content')
    @foreach($sections as $section)
@include("components.{$section->sectionType->slug}", ['data' => $section->data, 'school' => $school])    @endforeach
@endsection