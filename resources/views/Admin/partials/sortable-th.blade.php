{{--
    Sortable column header. Include with:
    @include('admin.partials.sortable-th', ['field' => 'name', 'label' => 'Name'])
    Optional: 'align' => 'right' for right-aligned columns (e.g. Actions doesn't use this partial).
--}}
@php
    $currentSort = request('sort');
    $currentDir = request('direction', 'desc');
    $isActive = $currentSort === $field;
    $nextDir = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';
    $params = array_merge(request()->except(['sort', 'direction', 'page']), ['sort' => $field, 'direction' => $nextDir]);
@endphp
<th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
    <a href="{{ request()->url() . '?' . http_build_query($params) }}" class="inline-flex items-center gap-1 hover:text-gray-800">
        {{ $label }}
        @if($isActive)
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($currentDir === 'asc')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                @endif
            </svg>
        @else
            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
            </svg>
        @endif
    </a>
</th>
