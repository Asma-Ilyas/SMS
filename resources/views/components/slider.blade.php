@php
$images = [];
if(isset($data['images'])){
    $images = is_array($data['images']) ? $data['images'] : json_decode($data['images'], true);
}
@endphp

<div class="slider-container" style="position: relative; width: 100%; overflow: hidden;">
    <div id="slider-{{ $section->id }}" 
         style="display: flex; flex-direction: row; transition: transform 0.5s ease-in-out; will-change: transform;">
        @foreach($images as $img)
            <div style="flex: 0 0 100%; min-width: 0;">  {{-- forces each slide to be full width --}}
                <img src="{{ asset($img) }}" style="width: 100%; height: 400px; object-fit: cover; display: block;">
            </div>
        @endforeach
    </div>

    {{-- Navigation arrows --}}
    <button id="prev-{{ $section->id }}" 
            style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); 
                   background: rgba(0,0,0,0.5); color: white; border: none; 
                   padding: 8px 12px; border-radius: 50%; cursor: pointer; z-index: 20;">
        &#10094;
    </button>
    <button id="next-{{ $section->id }}" 
            style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); 
                   background: rgba(0,0,0,0.5); color: white; border: none; 
                   padding: 8px 12px; border-radius: 50%; cursor: pointer; z-index: 20;">
        &#10095;
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('slider-{{ $section->id }}');
    if (!slider) return;
    const slides = slider.children;
    const total = slides.length;
    if (total === 0) return;

    let index = 0;

    function updateSlider() {
        slider.style.transform = `translateX(-${index * 100}%)`;
    }

    const prevBtn = document.getElementById('prev-{{ $section->id }}');
    const nextBtn = document.getElementById('next-{{ $section->id }}');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            index = (index - 1 + total) % total;
            updateSlider();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            index = (index + 1) % total;
            updateSlider();
        });
    }

    updateSlider(); // set initial position
});
</script>