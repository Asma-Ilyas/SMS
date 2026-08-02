<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use App\Models\Block;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        $floors = Floor::with('block')->orderBy('level')->paginate(20);
        return view('admin.floors.index', compact('floors'));
    }

    public function create()
    {
        $blocks = Block::orderBy('name')->get();
        return view('admin.floors.form', ['floor' => null, 'blocks' => $blocks]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:floors,code',
            'level' => 'required|integer|min:-3|max:50',
            'block_id' => 'nullable|exists:blocks,id',
        ]);
        Floor::create($validated);
        return redirect()->route('admin.floors.index')->with('success', 'Floor created.');
    }

    public function edit(Floor $floor)
    {
        $blocks = Block::orderBy('name')->get();
        return view('admin.floors.form', compact('floor', 'blocks'));
    }

    public function update(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:floors,code,' . $floor->id,
            'level' => 'required|integer|min:-3|max:50',
            'block_id' => 'nullable|exists:blocks,id',
        ]);
        $floor->update($validated);
        return redirect()->route('admin.floors.index')->with('success', 'Floor updated.');
    }

    public function destroy(Floor $floor)
    {
        if ($floor->rooms()->exists()) {
            return back()->with('error', 'Cannot delete floor that has rooms.');
        }
        $floor->delete();
        return back()->with('success', 'Floor deleted.');
    }
}