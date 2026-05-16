@php
    $cards = json_decode($data['cards'] ?? '[]', true);
    $style = json_decode($data['style'] ?? '{}', true);
    $bgColor = $style['background_color'] ?? '#f8f9fa';
    $padding = $style['padding'] ?? '60px 20px';
    $alignment = $style['alignment'] ?? 'center';
    $borderRadius = $style['card_border_radius'] ?? '12px';
    $boxShadow = $style['card_shadow'] ?? '0 8px 20px rgba(0,0,0,0.1)';
@endphp

<section style="background-color: {{ $bgColor }}; padding: {{ $padding }}; text-align: {{ $alignment }};">
    <div style="max-width: 1200px; margin: 0 auto;">
        @if(isset($data['title']))
            <h2 style="color: #0033cc; font-size: 2.5rem; margin-bottom: 40px;">{{ $data['title'] }}</h2>
        @endif

        <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            @foreach($cards as $card)
                @php
                    $cardColor = $card['color'] ?? '#333';
                    $textColor = '#ffffff';
                @endphp
                <div style="flex: 1; min-width: 280px; background: {{ $cardColor }}; color: {{ $textColor }}; 
                            border-radius: {{ $borderRadius }}; box-shadow: {{ $boxShadow }}; padding: 25px;">
                    <h3 style="font-size: 1.8rem; margin-bottom: 20px;">{{ $card['title'] }}</h3>
                    
                    @if($card['type'] == 'text')
                        <p style="line-height: 1.6;">{{ $card['content'] }}</p>
                    @elseif($card['type'] == 'list')
                        <ul style="list-style: none; padding: 0; text-align: left;">
                            @foreach($card['items'] as $item)
                                <li style="margin: 8px 0;">✓ {{ $item }}</li>
                            @endforeach
                        </ul>
                    @elseif($card['type'] == 'grouped')
                        @foreach($card['groups'] as $groupName => $items)
                            <div style="margin-bottom: 15px;">
                                <strong>{{ $groupName }}</strong>
                                <ul style="list-style: none; padding-left: 0; margin-top: 8px;">
                                    @foreach($items as $item)
                                        <li>✓ {{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>