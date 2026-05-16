@if(isset($currentSchool) && isset($schoolPages))
<!-- Right‑Side Panel (1/3 width, fixed) -->
<div id="side-panel" class="fixed inset-y-0 right-0 z-40 w-full sm:w-1/3 md:w-1/3 lg:w-1/3 xl:w-1/3 max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out">
    <!-- Close button -->
    <button id="close-panel" class="absolute top-4 right-4 text-gray-800 hover:text-red-600 focus:outline-none p-2">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Menu links -->
    <div class="flex flex-col items-start justify-start h-full pt-20 px-6 space-y-4">
        @foreach($schoolPages as $page)
            <a href="{{ route('page.show', ['schoolSlug' => $currentSchool->slug, 'pageSlug' => $page->slug]) }}" 
               class="text-2xl font-semibold text-gray-800 hover:text-blue-600 transition py-2 block">
                {{ $page->title }}
            </a>
        @endforeach
    </div>
</div>

<!-- Backdrop (dim background) -->
<div id="backdrop" class="fixed inset-0 z-30 bg-black bg-opacity-50 hidden transition-opacity duration-300"></div>

<script>
   const hamburger = document.getElementById('hamburger-fixed');
const panel = document.getElementById('side-panel');
const closeBtn = document.getElementById('close-panel');
const backdrop = document.getElementById('backdrop');

function openPanel() {
    panel.classList.remove('translate-x-full');
    backdrop.classList.remove('hidden');
    // REMOVE this line: document.body.style.overflow = 'hidden';
}

function closePanel() {
    panel.classList.add('translate-x-full');
    backdrop.classList.add('hidden');
    // REMOVE this line: document.body.style.overflow = '';
}

if (hamburger) {
    hamburger.addEventListener('click', openPanel);
}
closeBtn.addEventListener('click', closePanel);
backdrop.addEventListener('click', closePanel);
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && panel && !panel.classList.contains('translate-x-full')) {
        closePanel();
    }
});
</script>
@endif