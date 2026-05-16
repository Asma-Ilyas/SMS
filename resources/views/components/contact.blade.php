@php
    $style = json_decode($data['style'] ?? '{}', true);
    
    $bgColor = $style['background_color'] ?? '#f8f9fa';
    $textColor = $style['text_color'] ?? '#212529';
    $padding = $style['padding'] ?? '30px';
    $alignment = $style['alignment'] ?? 'left';
    $borderRadius = $style['border_radius'] ?? '0px';
    $boxShadow = $style['box_shadow'] ?? 'none';
    
    // Optional icons (you can add FontAwesome or similar)
    $showIcons = $style['show_icons'] ?? true;
@endphp

<div style="background-color: {{ $bgColor }}; color: {{ $textColor }}; 
            padding: {{ $padding }}; text-align: {{ $alignment }};
            border-radius: {{ $borderRadius }}; box-shadow: {{ $boxShadow }};">
    
    @if(isset($data['address']) || isset($data['phone']) || isset($data['email']))
        <div style="max-width: 600px; margin: 0 auto;">
            
            @if(isset($data['address']))
                <p style="margin: 10px 0;">
                    @if($showIcons)📍 @endif
                    {{ $data['address'] }}
                </p>
            @endif
            
            @if(isset($data['phone']))
                <p style="margin: 10px 0;">
                    @if($showIcons)📞 @endif
                    {{ $data['phone'] }}
                </p>
            @endif
            
            @if(isset($data['email']))
                <p style="margin: 10px 0;">
                    @if($showIcons)✉️ @endif
                    <a href="mailto:{{ $data['email'] }}" 
                       style="color: {{ $textColor }}; text-decoration: none;">
                        {{ $data['email'] }}
                    </a>
                </p>
            @endif
            
        </div>
    @endif
</div>