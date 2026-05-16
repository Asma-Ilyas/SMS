@extends('layouts.app')
@section('title', 'Result - ' . $exam->name)
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Marksheet</h1>
            <p class="text-gray-600">{{ $exam->name }} - {{ $student->name }}</p>
            <p class="text-sm text-gray-500">{{ $exam->class->grade->name ?? '' }} {{ $exam->class->stream->name ?? '' }} - {{ $exam->class->section }}</p>
        </div>

        <div class="p-6">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left">Subject</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Max Marks</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Obtained</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Percentage</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Grade</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marks as $mark)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $mark->subject->name }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $mark->max_marks }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $mark->marks_obtained }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $mark->percentage }}%</td>
                        <td class="border border-gray-300 px-4 py-2 text-center font-bold">{{ $mark->grade }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $mark->remarks ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-right" colspan="2">Total</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $summary['total_marks'] }} / {{ $summary['total_max'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $summary['percentage'] }}%</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $summary['grade'] }}</td>
                        <td class="border border-gray-300 px-4 py-2"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded">Print</button>
            </div>
        </div>
    </div>
</div>
@endsection