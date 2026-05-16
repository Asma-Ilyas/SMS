@props(['name', 'label' => null, 'checked' => false])
<div class="mb-4">
    <label class="inline-flex items-center">
        <input
            type="checkbox"
            name="{{ $name }}"
            value="1"
            {{ $attributes->merge(['class' => 'rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500']) }}
            @if($checked) checked @endif
        >
        @if($label)
            <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
        @endif
    </label>
    <x-form.error :name="$name" />
</div>