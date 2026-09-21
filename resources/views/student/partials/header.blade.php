<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
    @isset($subtitle)
        <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
    @endisset
</div>
@if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
