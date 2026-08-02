@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Please fix:</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium mb-1">Label</label>
        <input type="text" name="label" value="{{ old('label', $slot->label ?? '') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Start Time</label>
            <input type="time" name="start_time" value="{{ old('start_time', $slot->start_time ?? '') }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">End Time</label>
            <input type="time" name="end_time" value="{{ old('end_time', $slot->end_time ?? '') }}" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="type" class="w-full border rounded px-3 py-2" required>
            <option value="period" {{ old('type', $slot->type ?? '') == 'period' ? 'selected' : '' }}>Period</option>
            <option value="break" {{ old('type', $slot->type ?? '') == 'break' ? 'selected' : '' }}>Break</option>
            <option value="activity" {{ old('type', $slot->type ?? '') == 'activity' ? 'selected' : '' }}>Activity</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Period Number (for period type)</label>
        <input type="number" name="period_number" value="{{ old('period_number', $slot->period_number ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $slot->sort_order ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $slot->is_active ?? true) ? 'checked' : '' }} class="mr-2">
            Active
        </label>
    </div>
</div>