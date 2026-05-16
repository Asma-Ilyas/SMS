@extends('layouts.app')
@section('title', 'Result - ' . $exam->name)
@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-100 border-b">
            <h1 class="text-2xl font-bold">{{ $exam->name }} - Marksheet</h1>
            <p class="text-gray-700">{{ $student->name }} ({{ $student->class->full_name ?? '' }})</p>
        </div>
        <div class="p-6">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-50">
                    <tr><th class="border p-2">Subject</th><th class="border p-2">Max</th><th class="border p-2">Obtained</th><th class="border p-2">%</th><th class="border p-2">Grade</th></tr>
                </thead>
                <tbody>
                    @foreach($marks as $mark)
                    <tr>
                        <td class="border p-2">{{ $mark->subject->name }}</td>
                        <td class="border p-2 text-center">{{ $mark->max_marks }}</td>
                        <td class="border p-2 text-center">{{ $mark->marks_obtained }}</td>
                        <td class="border p-2 text-center">{{ $mark->percentage }}%</td>
                        <td class="border p-2 text-center font-bold">{{ $mark->grade }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td colspan="2" class="border p-2 text-right">Total</td>
                        <td class="border p-2 text-center">{{ $summary['total_marks'] }} / {{ $summary['total_max'] }}</td>
                        <td class="border p-2 text-center">{{ $summary['percentage'] }}%</td>
                        <td class="border p-2 text-center">{{ $summary['grade'] }}</td>
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