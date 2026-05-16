@php
    $style = json_decode($data['style'] ?? '{}', true);
    
    // Section-level styles
    $bgColor = $style['background_color'] ?? '#f5f5f5';
    $textColor = $style['text_color'] ?? '#333';
    $padding = $style['padding'] ?? '60px 20px';
    $alignment = $style['alignment'] ?? 'left';
    $fontSize = $style['font_size'] ?? '1rem';
    $lineHeight = $style['line_height'] ?? '1.5';
    $headingFontSize = $style['heading_font_size'] ?? '2rem';
    $headingFontWeight = $style['heading_font_weight'] ?? 'bold';
    
    // Content blocks
    $content = json_decode($data['content'] ?? '{}', true);
    $blocks = $content['blocks'] ?? [];
    
    // Separate text/list and image blocks
    $leftBlocks = [];
    $rightBlocks = [];
    foreach ($blocks as $block) {
        if (in_array($block['type'], ['text', 'list'])) {
            $leftBlocks[] = $block;
        } elseif (in_array($block['type'], ['image', 'images'])) {
            $rightBlocks[] = $block;
        }
    }
@endphp

<section style="background-color: {{ $bgColor }}; padding: {{ $padding }};">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 40px;">
        
        {{-- LEFT COLUMN: Text & Lists --}}
        <div style="flex: 1; min-width: 250px; font-size: {{ $fontSize }}; line-height: {{ $lineHeight }}; color: {{ $textColor }};">
            @foreach($leftBlocks as $block)
                @if($block['type'] == 'text')
                    {{-- Detect if this text block is likely a heading --}}
                    @php
                        $isHeading = $block['is_heading'] ?? (strlen($block['content']) < 80 && !str_contains($block['content'], '.') && !str_contains($block['content'], "\n"));
                    @endphp
                    @if($isHeading)
                        <div style="font-size: {{ $headingFontSize }}; font-weight: {{ $headingFontWeight }}; margin-bottom: 20px;">
                            {!! nl2br(e($block['content'])) !!}
                        </div>
                    @else
                        <div style="margin-bottom: 20px;">
                            {!! nl2br(e($block['content'])) !!}
                        </div>
                    @endif
                @elseif($block['type'] == 'list')
                    <ul style="text-align: left; margin-bottom: 20px;">
                        @foreach($block['items'] as $item)
                            <li style="margin-bottom: 8px;">{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            @endforeach
        </div>
        
        {{-- RIGHT COLUMN: Images --}}
        <div style="flex: 1; min-width: 250px;">
            @foreach($rightBlocks as $block)
                @if($block['type'] == 'image')
                    <img src="{{ asset($block['src']) }}" alt="{{ $block['alt'] ?? 'Image' }}" style="width: 100%; border-radius: 8px; margin-bottom: 20px;">
                @elseif($block['type'] == 'images')
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        @foreach($block['srcs'] as $src)
                            <img src="{{ asset($src) }}" alt="Image" style="width: calc(50% - 10px); border-radius: 8px;">
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
        
    </div>
</section>