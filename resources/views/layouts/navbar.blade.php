<nav class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-3 flex items-center justify-between">
    <!-- Left side: Welcome message -->
    <div class="flex items-center">
        <h2 class="text-lg font-semibold text-gray-800">
            Welcome back, <span class="text-green-600 font-bold">John</span>!
        </h2>
    </div>

    <!-- Right side: Bell icon + User profile -->
    <div class="flex items-center gap-4">
        <!-- Bell icon with notification dot -->
        <button class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
            </span>
        </button>

        <!-- User avatar + name -->
        <div class="flex items-center gap-2 cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                JD
            </div>
            <span class="text-sm font-medium text-gray-700 hidden sm:inline">John Doe</span>
        </div>
    </div>
</nav>