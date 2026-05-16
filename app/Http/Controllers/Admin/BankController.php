<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::latest()->paginate(15);
        return view('admin.banks.index', compact('banks'));
    }

    public function create()
    {
        return view('admin.banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'branch_name'     => 'required|string|max:255',
            'account_title'   => 'required|string|max:255',
            'account_number'  => 'required|string|max:100',
            'iban'            => 'nullable|string|max:50',
            'routing_number'  => 'nullable|string|max:50',
            'address'         => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        Bank::create($request->all());

        return redirect()->route('admin.banks.index')
            ->with('success', 'Bank added successfully.');
    }

    public function show(Bank $bank)
    {
        return view('admin.banks.show', compact('bank'));
    }

    public function edit(Bank $bank)
    {
        return view('admin.banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'branch_name'     => 'required|string|max:255',
            'account_title'   => 'required|string|max:255',
            'account_number'  => 'required|string|max:100',
            'iban'            => 'nullable|string|max:50',
            'routing_number'  => 'nullable|string|max:50',
            'address'         => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $bank->update($request->all());

        return redirect()->route('admin.banks.index')
            ->with('success', 'Bank updated successfully.');
    }

    public function destroy(Bank $bank)
    {
        // Optionally check if bank is used in invoices
        if ($bank->invoices()->exists()) {
            return redirect()->route('admin.banks.index')
                ->with('error', 'Cannot delete bank because it is linked to existing invoices.');
        }
        $bank->delete();
        return redirect()->route('admin.banks.index')
            ->with('success', 'Bank deleted successfully.');
    }
}