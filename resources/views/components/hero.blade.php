@php
    $style = json_decode($data['style'] ?? '{}', true);
    $overlayColor = $style['overlay_color'] ?? 'rgba(0,0,0,0.5)';
    $textColor = $style['text_color'] ?? '#ffffff';
    $textAlign = $style['text_align'] ?? 'right';
    $justifyContent = $textAlign === 'center' ? 'center' : ($textAlign === 'right' ? 'flex-end' : 'flex-start');
@endphp

<div class="hero-section" style="position: relative; width: 100%; height: 500px; overflow: hidden;">
    {{-- HAMBURGER BUTTON (only on hero) --}}
    <button id="hamburger-fixed" class="absolute top-4 right-4 z-20 bg-white rounded-full shadow-lg p-2 focus:outline-none hover:bg-gray-100 transition">
        <svg class="h-8 w-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <img src="{{ asset($data['image'] ?? 'placeholder.jpg') }}" 
         alt="Hero Background"
         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;">

    {{-- Dynamic overlay --}}
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                background: {{ $overlayColor }}; z-index: 1;"></div>

    {{-- Dynamic logo --}}
    @if(!empty($data['logo']))
        <img src="{{ asset($data['logo']) }}" 
             alt="School Logo"
             style="position: absolute; top: 20px; left: 20px; height: 100px; width: auto; z-index: 2;">
    @endif

    <div style="position: absolute; top: 0; right: 0; width: 100%; height: 100%; 
                display: flex; align-items: center; justify-content: {{ $justifyContent }}; 
                padding: 0 40px; z-index: 2;">
        <div style="max-width: 600px; text-align: {{ $textAlign }};">
            @if(isset($data['heading']))
                <h1 style="font-size: 4rem; font-weight: 800; color: {{ $textColor }}; 
                           margin-bottom: 0.5rem; line-height: 1.2; text-shadow: 2px 2px 8px rgba(0,0,0,0.5);">
                    {{ $data['heading'] }}
                </h1>
            @endif

            @if(isset($data['subheading']))
                <p style="font-size: 1.5rem; font-weight: 700; color: {{ $textColor }}; 
                          text-shadow: 1px 1px 4px rgba(0,0,0,0.5);">
                    {{ $data['subheading'] }}
                </p>
            @endif
        </div>
    </div>
</div>

{{-- Include the side panel (only once, but safe to include here) --}}
@includeIf('components.side-panel')