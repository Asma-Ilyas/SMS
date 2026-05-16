@php
$images = is_array($data['images'] ?? null) ? $data['images'] : json_decode($data['images'] ?? '[]', true);
@endphp

<div class="grid grid-cols-3 gap-2 p-8" style="{{ $section->style_string }}">
    @foreach($images as $img)
      <img src="{{ asset($img) }}" class="w-full h-64 object-cover rounded-md"> <!-- taller images -->
    @endforeach
</div>