@extends('layouts.app')
@section('title', 'My Leaves')
@section('content')
@php $fmt = fn($d) => \Carbon\Carbon::parse($d)->format('d M Y'); @endphp
<div class="space-y-6">
    @include('teacher.partials.header', ['title' => 'My Leaves', 'subtitle' => 'Leave requests and usage for ' . $year])

    <div class="flex gap-3"><a href="{{ route('teacher.leaves.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">+ Request Leave</a></div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([['Sick (approved days)', $usage['sick'], 'text-red-600'], ['Casual (approved days)', $usage['casual'], 'text-yellow-600'], ['Annual (approved days)', $usage['annual'], 'text-blue-600'], ['Pending requests', $pending, 'text-indigo-600']] as [$l, $v, $c])
            <div class="bg-white rounded-xl border shadow-sm p-5"><div class="text-xs uppercase text-gray-500">{{ $l }}</div><div class="text-3xl font-bold {{ $c }}">{{ $v }}</div></div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        @if($leaves->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">You have not requested any leave yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Type</th><th class="px-4 py-3">From</th><th class="px-4 py-3">To</th><th class="px-4 py-3">Days</th><th class="px-4 py-3">Reason</th><th class="px-4 py-3">Status</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($leaves as $l)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ ucfirst($l->type) }}</td>
                            <td class="px-4 py-2">{{ $fmt($l->start_date) }}</td>
                            <td class="px-4 py-2">{{ $fmt($l->end_date) }}</td>
                            <td class="px-4 py-2">{{ $l->days }}</td>
                            <td class="px-4 py-2 text-gray-600 max-w-xs truncate" title="{{ $l->reason }}">{{ $l->reason }}</td>
                            <td class="px-4 py-2">@include('teacher.partials.badge', ['status' => $l->status])</td>
                            <td class="px-4 py-2 text-right">
                                @if($l->status === 'pending')
                                    <form method="POST" action="{{ route('teacher.leaves.destroy', $l->id) }}" onsubmit="return confirm('Cancel this leave request?')">
                                        @csrf @method('DELETE')<button class="text-red-600 hover:underline text-sm">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
