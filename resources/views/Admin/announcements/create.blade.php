@extends('layouts.app')

@section('title', 'New announcement')

@section('content')
<div class="max-w-2xl space-y-6">
    <div>
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to announcements</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">New announcement</h1>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.announcements.store') }}"
          x-data="{ audience: '{{ old('audience', 'all') }}' }"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input id="title" name="title" type="text" maxlength="150" required value="{{ old('title') }}"
                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea id="message" name="message" rows="5" maxlength="2000" required
                      class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="audience" class="block text-sm font-medium text-gray-700">Send to</label>
                <select id="audience" name="audience" x-model="audience" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(\App\Models\Announcement::AUDIENCES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select id="priority" name="priority" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(\App\Models\Announcement::PRIORITIES as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div x-show="audience === 'section'" x-cloak>
            <label for="class_section_id" class="block text-sm font-medium text-gray-700">Class section</label>
            <select id="class_section_id" name="class_section_id" :disabled="audience !== 'section'" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Choose a section…</option>
                @foreach($sections as $s)
                    <option value="{{ $s['id'] }}" @selected((int) old('class_section_id') === $s['id'])>{{ $s['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="link" class="block text-sm font-medium text-gray-700">Link <span class="font-normal text-gray-400">(optional)</span></label>
            <input id="link" name="link" type="text" placeholder="/student/fees" value="{{ old('link') }}"
                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-gray-400">A page inside this system that the notification should open, starting with “/”.</p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button class="px-5 py-2 rounded-xl bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700">Send announcement</button>
        </div>
    </form>
</div>
@endsection