<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        $blocks = Block::orderBy('name')->paginate(20);
        return view('admin.blocks.index', compact('blocks'));
    }

    public function create()
    {
        return view('admin.blocks.form', ['block' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:blocks,code',
        ]);
        Block::create($validated);
        return redirect()->route('admin.blocks.index')->with('success', 'Block created.');
    }

    public function edit(Block $block)
    {
        return view('admin.blocks.form', compact('block'));
    }

    public function update(Request $request, Block $block)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:blocks,code,' . $block->id,
        ]);
        $block->update($validated);
        return redirect()->route('admin.blocks.index')->with('success', 'Block updated.');
    }

    public function destroy(Block $block)
    {
        if ($block->rooms()->exists()) {
            return back()->with('error', 'Cannot delete block that has rooms.');
        }
        $block->delete();
        return back()->with('success', 'Block deleted.');
    }
}