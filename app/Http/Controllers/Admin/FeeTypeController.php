<?php
// app/Http/Controllers/Admin/FeeTypeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeSubmissionType;
use App\Http\Requests\StoreFeeTypeRequest;
use App\Http\Requests\UpdateFeeTypeRequest;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index()
    {
        $feeTypes = FeeSubmissionType::latest()->paginate(15);
        return view('admin.fees.types-index', compact('feeTypes'));
    }

    public function create()
    {
        return view('admin.fees.types-create');
    }

    public function store(StoreFeeTypeRequest $request)
    {
        FeeSubmissionType::create($request->validated());

        return redirect()->route('admin.fee-types.index')
            ->with('success', 'Fee type created successfully.');
    }

    public function show(FeeSubmissionType $feeType)
    {
        return view('admin.fees.types-show', compact('feeType'));
    }

    public function edit(FeeSubmissionType $feeType)
    {
        return view('admin.fees.types-edit', compact('feeType'));
    }

    public function update(UpdateFeeTypeRequest $request, FeeSubmissionType $feeType)
    {
        $feeType->update($request->validated());

        return redirect()->route('admin.fee-types.index')
            ->with('success', 'Fee type updated successfully.');
    }

    public function destroy(FeeSubmissionType $feeType)
    {
        $feeType->delete();

        return redirect()->route('admin.fee-types.index')
            ->with('success', 'Fee type deleted successfully.');
    }
}