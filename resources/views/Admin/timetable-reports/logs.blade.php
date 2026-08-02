{{-- resources/views/admin/timetable-reports/logs.blade.php --}}
@extends('layouts.app')
@section('title', 'Generation Logs')

@section('content')
<div class="py-6">
<div class="max-w-5xl mx-auto px-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-900">Timetable Generation Logs</h1>
        <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg">← Back</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Generated At</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Entries</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Warnings</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Errors</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
                <tr>
                    <td class="px-4 py-3 text-gray-700">
                        {{ \Carbon\Carbon::parse($log->generated_at)->format('d M Y, g:i A') }}
                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($log->generated_at)->diffForHumans() }}</div>
                    </td>
                    <td class="px-4 py-3 text-center font-semibold text-indigo-600">{{ $log->total_entries }}</td>
                    <td class="px-4 py-3 text-center">
                        @php $errCount = is_array($log->errors) ? count($log->errors) : 0; @endphp
                        @if($errCount > 0)
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs">{{ $errCount }}</span>
                        @else
                            <span class="text-green-500 text-xs">✓ None</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if(is_array($log->errors) && count($log->errors))
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($log->errors as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No generation logs yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
</div>
@endsection