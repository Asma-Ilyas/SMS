@extends('layouts.app')
@section('title', 'Certificates')
@section('content')
@php $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—'; @endphp
<div class="space-y-6">
    @include('student.partials.header', ['title' => 'Certificates', 'subtitle' => 'Certificates issued to you'])

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <div class="px-5 py-4 border-b font-semibold text-gray-800">Issued Certificates</div>
        @if($certificates->isEmpty())
            <div class="p-6 text-sm text-gray-500">No certificates have been issued to you yet.</div>
        @else
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Certificate</th><th class="px-4 py-3">Issued</th><th class="px-4 py-3">Remarks</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($certificates as $c)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $c->title }}<div class="text-xs text-gray-400">{{ $c->description }}</div></td>
                            <td class="px-4 py-3">{{ $fmt($c->issue_date) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $c->remarks ?: '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($c->certificate_file)
                                    <a href="{{ route('student.certificates.download', $c->id) }}" class="text-indigo-600 hover:underline">⬇ Download</a>
                                @else
                                    <span class="text-xs text-gray-400">File pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($transferCertificates->isNotEmpty())
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <div class="px-5 py-4 border-b font-semibold text-gray-800">Transfer Certificates</div>
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left"><tr><th class="px-4 py-3">Certificate #</th><th class="px-4 py-3">Includes</th><th class="px-4 py-3">Issued</th><th class="px-4 py-3">Remarks</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($transferCertificates as $t)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $t->certificate_number }}</td>
                            <td class="px-4 py-3">{{ collect($t->types)->map(fn($x) => is_array($x) ? ($x['title'] ?? json_encode($x)) : $x)->implode(', ') ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $fmt($t->issued_date) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $t->remarks ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
