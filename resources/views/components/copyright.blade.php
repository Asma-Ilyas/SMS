@php
    $style = json_decode($data['style'] ?? '{}', true);
    
    $bgColor = $style['background_color'] ?? '#000000';      // black default
    $textColor = $style['text_color'] ?? '#ffffff';
    $linkColor = $style['link_color'] ?? '#cccccc';
    $padding = $style['padding'] ?? '12px 20px';
    $alignment = $style['alignment'] ?? 'center';
    $fontSize = $style['font_size'] ?? '0.9rem';
    
    $links = json_decode($data['links'] ?? '[]', true);
@endphp

<div style="background-color: {{ $bgColor }}; color: {{ $textColor }}; 
            padding: {{ $padding }}; text-align: {{ $alignment }}; 
            font-size: {{ $fontSize }};">
    
    @if(isset($data['text']))
        <p style="margin: 0 0 5px 0;">{{ $data['text'] }}</p>
    @endif

    @if(!empty($links))
        <div>
            @foreach($links as $index => $link)
                @if($index > 0) <span style="color: {{ $linkColor }}; margin: 0 5px;">|</span> @endif
                <span style="color: {{ $linkColor }};">{{ $link }}</span>
            @endforeach
        </div>
    @endif
</div>