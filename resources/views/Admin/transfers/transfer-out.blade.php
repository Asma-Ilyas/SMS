@extends('layouts.app')

@section('title', 'Transfer Student Out')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Transfer Out: {{ $student->full_name }}</h2>
        <form method="POST" action="{{ route('admin.students.transfer-out', $student) }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div><label>Transfer to School *</label><input type="text" name="to_school" class="w-full border rounded px-3 py-2" required></div>
                <div><label>Transfer Date *</label><input type="date" name="transfer_date" class="w-full border rounded px-3 py-2" required></div>
                <div><label>Reason</label><textarea name="reason" rows="3" class="w-full border rounded px-3 py-2"></textarea></div>
                <div><label>Document (TC, etc.)</label><input type="file" name="document" accept=".pdf,.jpg,.png"></div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.students.show', $student) }}" class="px-4 py-2 bg-gray-300 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Confirm Transfer Out</button>
            </div>
        </form>
    </div>
</div>
@endsection