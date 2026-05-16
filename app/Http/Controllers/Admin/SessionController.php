<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = AcademicSession::latest()->paginate(15);
        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sessions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request)
    {
        $validated = $request->validated();

        // If setting as active, remove active flag from others
        if ($request->has('is_active') && $request->is_active) {
            AcademicSession::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicSession::create($validated);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Academic session created successfully.');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicSession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, AcademicSession $session)
    {
        $validated = $request->validated();

        if ($request->has('is_active') && $request->is_active) {
            AcademicSession::where('id', '!=', $session->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $session->update($validated);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicSession $session)
    {
         $session->delete();
        return redirect()->route('admin.sessions.index')
            ->with('success', 'Session deleted.');
    
    }
}
