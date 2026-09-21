<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TransportHostelAlertService;

class TransportHostelAlertController extends Controller
{
    public function index(TransportHostelAlertService $alerts)
    {
        $data = $alerts->generate();
        return view('admin.transport-hostel-alerts.index', ['alerts' => $data]);
    }

    /**
     * JSON endpoint for the navbar bell dropdown (Alpine fetches this on load).
     */
    public function summary(TransportHostelAlertService $alerts)
    {
        $data = $alerts->generate();
        $flat = collect($data)->flatten(1)->sortBy('date')->take(8)->values();

        return response()->json([
            'count' => collect($data)->sum(fn ($g) => count($g)),
            'items' => $flat,
        ]);
    }
}
