@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $classes = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'secondary' => 'bg-gray-500 hover:bg-gray-600 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    ][$variant] ?? 'bg-gray-600 hover:bg-gray-700 text-white';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "px-4 py-2 rounded-md font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 $classes"]) }}
>
    {{ $slot }}
</button>