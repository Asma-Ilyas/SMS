{{-- resources/views/components/mission.blade.php --}}
<div class="p-8" style="{{ $section->style_string }}">
    <p style="color: {{ $section->text_color ?? '#000' }}">
        {{ $data['text'] ?? '' }}
    </p>
</div>