@extends('layouts.app')
@section('title', 'Academic Report')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="text-center border-b pb-4">
            <h1 class="text-2xl font-bold">{{ $student->name }}</h1>
            <p class="text-gray-600">{{ $student->class->full_name ?? '' }}</p>
            <p class="text-gray-500">Academic Progress Report</p>
        </div>

        @foreach($report as $examId => $data)
            <div class="mt-8 border-t pt-4">
                <h2 class="text-xl font-semibold text-blue-800">{{ $data['exam']->name }}</h2>
                <table class="min-w-full border-collapse mt-2">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="border p-2">Subject</th><th class="border p-2">Max</th><th class="border p-2">Obtained</th><th class="border p-2">%</th><th class="border p-2">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['marks'] as $mark)
                        <tr>
                            <td class="border p-2">{{ $mark->subject->name }}</td>
                            <td class="border p-2 text-center">{{ $mark->max_marks }}</td>
                            <td class="border p-2 text-center">{{ $mark->marks_obtained }}</td>
                            <td class="border p-2 text-center">{{ $mark->percentage }}%</td>
                            <td class="border p-2 text-center">{{ $mark->grade }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="2" class="border p-2 text-right">Total</td>
                            <td class="border p-2 text-center">{{ $data['summary']['total_marks'] }}/{{ $data['summary']['total_max'] }}</td>
                            <td class="border p-2 text-center">{{ $data['summary']['percentage'] }}%</td>
                            <td class="border p-2 text-center">{{ $data['summary']['grade'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach

        <div class="mt-8 p-4 bg-gray-100 rounded-lg">
            <h3 class="text-lg font-bold">Overall Performance</h3>
            <div class="grid grid-cols-3 gap-4 mt-2">
                <div>Total Marks: {{ $overall['total_marks'] }}/{{ $overall['total_max'] }}</div>
                <div>Percentage: {{ $overall['percentage'] }}%</div>
                <div>Final Grade: <span class="font-bold text-green-700">{{ $overall['grade'] }}</span></div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded">Print Report</button>
        </div>
    </div>
</div>
@endsection