@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('page_subtitle', 'Your inbox')

@section('content')
@php
    // level => [icon circle classes, icon path]
    $levels = [
        'info'    => ['bg-blue-100 text-blue-600',    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'success' => ['bg-green-100 text-green-600',  'M5 13l4 4L19 7'],
        'warning' => ['bg-yellow-100 text-yellow-600', 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
        'danger'  => ['bg-red-100 text-red-600',      'M6 18L18 6M6 6l12 12'],
    ];
    $chip = fn ($active) => $active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-300';
@endphp

<div class="space-y-6 max-w-4xl">

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">
@if($unreadCount) You have {{ $unreadCount }} unread {{ \Illuminate\Support\Str::plural('notification', $unreadCount) }}. @else You're all caught up. @endif            </p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('notifications.read-all') }}">@csrf
                <button class="px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50" @disabled(!$unreadCount)>Mark all as read</button>
            </form>
            <form method="POST" action="{{ route('notifications.clear-read') }}" onsubmit="return confirm('Delete all read notifications?')">@csrf @method('DELETE')
                <button class="px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 hover:bg-gray-50">Clear read</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-2 text-sm">
        <a href="{{ route('notifications.index') }}" class="px-3 py-1.5 rounded-full {{ $chip($filter === 'all' && !$category) }}">All</a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="px-3 py-1.5 rounded-full {{ $chip($filter === 'unread' && !$category) }}">Unread @if($unreadCount)({{ $unreadCount }})@endif</a>
        @foreach($categories as $key => $label)
            <a href="{{ route('notifications.index', ['category' => $key, 'filter' => $filter === 'unread' ? 'unread' : null]) }}" class="px-3 py-1.5 rounded-full {{ $chip($category === $key) }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-50 overflow-hidden">
        @forelse($notifications as $n)
            @php
                $d      = $n->data;
                $level  = $levels[$d['level'] ?? 'info'] ?? $levels['info'];
                $unread = $n->read_at === null;
            @endphp
            <div class="flex gap-4 px-5 py-4 {{ $unread ? 'bg-indigo-50/40' : '' }}">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $level[0] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $level[1] }}"/></svg>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm {{ $unread ? 'font-semibold text-gray-900' : 'font-medium text-gray-700' }}">{{ $d['title'] ?? 'Notification' }}</p>
                        <span class="shrink-0 text-xs text-gray-400" title="{{ $n->created_at }}">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-600 break-words">{{ $d['message'] ?? '' }}</p>

                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-500">{{ $categories[$d['category'] ?? 'general'] ?? 'Other' }}</span>
                        @if(!empty($d['url']))
                            <a href="{{ route('notifications.open', $n->id) }}" class="font-medium text-indigo-600 hover:underline">Open</a>
                        @endif
                        @if($unread)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">@csrf<button class="text-gray-500 hover:text-gray-800">Mark as read</button></form>
                        @endif
                        <form method="POST" action="{{ route('notifications.destroy', $n->id) }}">@csrf @method('DELETE')<button class="text-gray-400 hover:text-red-600">Delete</button></form>
                    </div>
                </div>

                @if($unread)<span class="shrink-0 mt-2 w-2 h-2 rounded-full bg-indigo-500" title="Unread"></span>@endif
            </div>
        @empty
            <div class="px-6 py-14 text-center text-sm text-gray-400">
                @if($filter === 'unread' || $category) Nothing matches this filter. @else No notifications yet. @endif
            </div>
        @endforelse
    </div>

    <div>{{ $notifications->links() }}</div>
</div>
@endsection