{{--
    Reusable search bar. Include with:
    @include('admin.partials.search-bar', ['placeholder' => 'Search vehicles...'])
--}}
<div class="mb-4">
    <form method="GET" class="flex gap-2">
        @foreach(request()->except(['search', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <div class="relative flex-1 max-w-sm">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder ?? 'Search...' }}"
                   class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-300 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
        </div>
        <button class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">Search</button>
        @if(request('search'))
        <a href="{{ request()->url() }}" class="inline-flex items-center px-4 py-2 text-gray-500 text-sm hover:text-gray-700 transition">Clear</a>
        @endif
    </form>
</div>
