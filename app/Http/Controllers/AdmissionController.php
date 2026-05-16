<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdmissionController extends Controller
{
   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'gender'    => 'required|in:Male,Female,other',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'dob'       => 'nullable|date',
            'city_campus' => 'nullable|string|max:255',
            'class'     => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $application = AdmissionApplication::create($request->all());

        return response()->json([
            'message' => 'Application submitted successfully! We will contact you soon.',
            'data'    => $application
        ], 201);
    }
}
