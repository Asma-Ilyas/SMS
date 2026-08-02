<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderBy('start_date', 'desc')->paginate(15);
        return view('admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('admin.sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:20|unique:academic_sessions',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'is_active'   => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        AcademicSession::create($validated);
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Academic session created.');
    }

    public function edit(AcademicSession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    public function update(Request $request, AcademicSession $session)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:20|unique:academic_sessions,name,' . $session->id,
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'is_active'   => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        $session->update($validated);
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Academic session updated.');
    }

    public function destroy(AcademicSession $session)
    {
        $session->delete();
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Academic session deleted.');
    }
}