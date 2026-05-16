@php
    $style = json_decode($data['style'] ?? '{}', true);
    $bgImage = !empty($style['background_image']) ? asset($style['background_image']) : null;
    
    // Get the target form page slug from style JSON (default: 'admission-form')
    $formPageSlug = $style['form_page_slug'] ?? 'admission-form';
    // Build the URL using the school slug (passed from controller) and the form page slug
    $formUrl = url("/welcome/{$school->slug}/{$formPageSlug}");
@endphp

@if($bgImage)
    {{-- Background image with overlay --}}
    <section style="position: relative; background-image: url('{{ $bgImage }}'); background-size: cover; background-position: center; padding: {{ $style['padding'] ?? '80px 20px' }};">
        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: {{ $style['overlay_color'] ?? 'rgba(0,0,0,0.5)' }}; z-index:1;"></div>
        <div style="position: relative; z-index:2; text-align: {{ $style['alignment'] ?? 'center' }}; color: {{ $style['text_color'] ?? '#fff' }};">
            <h2>{{ $data['heading'] ?? '' }}</h2>
            <p>{{ $data['subheading'] ?? '' }}</p>
            @if(!empty($data['button_text']))
                <a href="{{ $formUrl }}" style="display: inline-block; background: {{ $style['button_background'] ?? '#fff' }}; color: {{ $style['button_text_color'] ?? '#0033cc' }}; padding: 12px 30px; border-radius: {{ $style['button_border_radius'] ?? '50px' }}; text-decoration: none;">
                    {{ $data['button_text'] }}
                </a>
            @endif
        </div>
    </section>
@else
    {{-- Two‑column layout (image left/right) --}}
    @php
        $layout = $style['layout'] ?? 'image-right-text-left';
        $imageFirst = ($layout === 'image-left-text-right');
    @endphp
    <section style="background-color: {{ $style['background_color'] ?? '#f0f7ff' }}; padding: {{ $style['padding'] ?? '80px 20px' }};">
        <div style="display: flex; flex-wrap: wrap; gap: 40px; align-items: center; max-width:1200px; margin:0 auto;">
            @if(!empty($data['image']))
                <div style="flex:1; order: {{ $imageFirst ? 1 : 2 }};">
                    <img src="{{ asset($data['image']) }}" style="width:100%; border-radius:12px;">
                </div>
            @endif
            <div style="flex:1; order: {{ $imageFirst ? 2 : 1 }}; text-align: {{ $style['alignment'] ?? 'left' }};">
                <h2 style="color:{{ $style['text_color'] ?? '#0033cc' }}">{{ $data['heading'] ?? '' }}</h2>
                <p style="color:{{ $style['text_color'] ?? '#0033cc' }}">{{ $data['subheading'] ?? '' }}</p>
                @if(!empty($data['button_text']))
                    <a href="{{ $formUrl }}" style="display:inline-block; background:{{ $style['button_background'] ?? '#0033cc' }}; color:{{ $style['button_text_color'] ?? '#fff' }}; padding:12px 30px; border-radius:{{ $style['button_border_radius'] ?? '50px' }}; text-decoration: none;">
                        {{ $data['button_text'] }}
                    </a>
                @endif
            </div>
        </div>
    </section>
@endif