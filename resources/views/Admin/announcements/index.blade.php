@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
@php
    $priorityStyle = ['normal' => 'bg-gray-100 text-gray-600', 'important' => 'bg-yellow-100 text-yellow-700', 'urgent' => 'bg-red-100 text-red-700'];
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Announcements</h1>
            <p class="text-sm text-gray-500 mt-1">Send a message to admins, teachers, students or one class section. It appears in their notification bell.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700">New announcement</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-5 py-3 font-medium">Announcement</th>
                    <th class="px-5 py-3 font-medium">Audience</th>
                    <th class="px-5 py-3 font-medium">Priority</th>
                    <th class="px-5 py-3 font-medium">Sent to</th>
                    <th class="px-5 py-3 font-medium">Sent</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($announcements as $a)
                    <tr>
                        <td class="px-5 py-3 max-w-md">
                            <p class="font-medium text-gray-800">{{ $a->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $a->message }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $a->audienceLabel() }}</td>
                        <td class="px-5 py-3"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityStyle[$a->priority] ?? $priorityStyle['normal'] }}">{{ ucfirst($a->priority) }}</span></td>
                        <td class="px-5 py-3 text-gray-600">{{ $a->recipients_count }}</td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $a->created_at->format('d M Y, h:i A') }}<span class="block text-xs text-gray-400">{{ $a->creator?->name }}</span></td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Delete this announcement? It will also disappear from everyone\'s notifications.')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No announcements yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $announcements->links() }}</div>
</div>
@endsection