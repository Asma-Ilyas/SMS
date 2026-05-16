<div class="grid grid-cols-3 gap-4 text-center p-8" style="{{ $section->style_string }}">
    <div>
        <h3>{{ $data['students'] ?? '' }}</h3>
        <p>Students</p>
    </div>
    <div>
        <h3>{{ $data['teachers'] ?? '' }}</h3>
        <p>Teachers</p>
    </div>
    <div>
        <h3>{{ $data['classes'] ?? '' }}</h3>
        <p>Classes</p>
    </div>
</div>