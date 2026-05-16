@php
    $style = json_decode($data['style'] ?? '{}', true);
    
    $bgColor = $style['background_color'] ?? '#0a0a2a';
    $textColor = $style['text_color'] ?? '#ffffff';
    $linkColor = $style['link_color'] ?? '#cccccc';
    $padding = $style['padding'] ?? '50px 20px 30px';
    $logoWidth = $style['logo_width'] ?? '120px';
    
    $socialLinks = json_decode($data['social_links'] ?? '[]', true);
@endphp

<footer style="background-color: {{ $bgColor }}; color: {{ $textColor }}; padding: {{ $padding }};">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between;">
        
        {{-- LEFT COLUMN: Logo & Description --}}
        <div style="flex: 1; min-width: 250px; text-align: left;">
            @if(!empty($data['logo']))
                <img src="{{ asset($data['logo']) }}" alt="Logo" style="width: {{ $logoWidth }}; height: auto; margin-bottom: 15px;">
            @endif
            @if(!empty($data['description']))
                <p style="margin: 10px 0; line-height: 1.5;">{{ $data['description'] }}</p>
            @endif
        </div>
        
        {{-- RIGHT COLUMN: Contact Info & Social Links --}}
        <div style="flex: 1; min-width: 250px; text-align: right;">
            @if(!empty($data['phone']))
                <p style="margin: 8px 0;">📞 <span style="color: {{ $linkColor }};">{{ $data['phone'] }}</span></p>
            @endif
            @if(!empty($data['email']))
                <p style="margin: 8px 0;">✉️ <a href="mailto:{{ $data['email'] }}" style="color: {{ $linkColor }}; text-decoration: none;">{{ $data['email'] }}</a></p>
            @endif
            @if(!empty($data['address']))
                <p style="margin: 8px 0;">📍 {{ $data['address'] }}</p>
            @endif
            
            {{-- Social Links --}}
            @if(!empty($socialLinks))
                <div style="margin-top: 20px; display: flex; gap: 15px; justify-content: flex-end;">
                    @foreach($socialLinks as $social)
                        <a href="{{ $social['url'] }}" target="_blank" style="color: {{ $linkColor }}; font-size: 1.5rem; text-decoration: none;">
                            <i class="fab fa-{{ $social['platform'] }}"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        
    </div>
</footer>