<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStreamRequest;
use App\Http\Requests\UpdateStreamRequest;
use App\Models\Stream;

class StreamController extends Controller
{
    public function index()
    {
        $streams = Stream::latest()->paginate(15);
        return view('admin.streams.index', compact('streams'));
    }

    public function create()
    {
        return view('admin.streams.create');
    }

    public function store(StoreStreamRequest $request)
    {
        Stream::create($request->validated());
        return redirect()->route('admin.streams.index')->with('success', 'Stream created.');
    }

    public function edit(Stream $stream)
    {
        return view('admin.streams.edit', compact('stream'));
    }

    public function update(UpdateStreamRequest $request, Stream $stream)
    {
        $stream->update($request->validated());
        return redirect()->route('admin.streams.index')->with('success', 'Stream updated.');
    }

    public function destroy(Stream $stream)
    {
        if ($stream->classes()->exists() || $stream->electiveTracks()->exists()) {
            return back()->with('error', 'Cannot delete stream because it has associated classes or elective tracks.');
        }
        $stream->delete();
        return redirect()->route('admin.streams.index')->with('success', 'Stream deleted.');
    }
}