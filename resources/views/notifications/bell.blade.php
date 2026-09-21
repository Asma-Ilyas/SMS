{{-- Header bell. Put  @include('notifications.bell')  in your top navigation bar. Works for every role. --}}
@auth
<style>[x-cloak] { display: none !important; }</style>
<div class="relative"
     x-data="{
        open: false,
        unread: {{ (int) auth()->user()->unreadNotifications()->count() }},
        items: [],
        dot(level) {
            return { info: 'bg-blue-500', success: 'bg-green-500', warning: 'bg-yellow-500', danger: 'bg-red-500' }[level] || 'bg-blue-500';
        },
        async load() {
            try {
                const r = await fetch('{{ route('notifications.feed') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (!r.ok) return;
                const d = await r.json();
                this.unread = d.unread;
                this.items = d.items;
            } catch (e) {}
        },
        init() { this.load(); setInterval(() => this.load(), 60000); }
     }"
     @click.outside="open = false">

    <button type="button" @click="open = !open; if (open) load()"
            class="relative p-2 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition" aria-label="Notifications">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="unread > 0" x-cloak x-text="unread > 99 ? '99+' : unread"
              class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-red-500 text-white text-[10px] font-semibold flex items-center justify-center"></span>
    </button>

    <div x-show="open" x-cloak x-transition.opacity
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <p class="font-semibold text-gray-800 text-sm">Notifications</p>
            <span class="text-xs text-gray-400" x-text="unread ? unread + ' unread' : 'All read'"></span>
        </div>

        <div class="max-h-96 overflow-y-auto divide-y divide-gray-50">
            <template x-for="i in items" :key="i.id">
                <a :href="i.open_url" class="flex gap-3 px-4 py-3 hover:bg-gray-50" :class="i.read ? '' : 'bg-indigo-50/40'">
                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0" :class="dot(i.level)"></span>
                    <span class="min-w-0">
                        <span class="block text-sm text-gray-800 truncate" :class="i.read ? 'font-medium' : 'font-semibold'" x-text="i.title"></span>
                        <span class="block text-xs text-gray-500 truncate" x-text="i.message"></span>
                        <span class="block text-[11px] text-gray-400 mt-0.5" x-text="i.time"></span>
                    </span>
                </a>
            </template>
            <p x-show="items.length === 0" class="px-4 py-8 text-center text-sm text-gray-400">No notifications yet.</p>
        </div>

        <a href="{{ route('notifications.index') }}" class="block text-center px-4 py-3 border-t border-gray-100 text-sm font-medium text-indigo-600 hover:bg-gray-50">View all notifications</a>
    </div>
</div>
@endauth