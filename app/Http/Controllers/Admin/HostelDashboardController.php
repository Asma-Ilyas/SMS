<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\StudentHostelAllocation;
use App\Models\StudentHostelFeePayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HostelDashboardController extends Controller
{
    public function index()
    {
        $totalCapacity = HostelRoom::sum('capacity');
        $totalOccupied = HostelRoom::sum('current_occupancy');

        $stats = [
            'total_hostels' => Hostel::where('is_active', true)->count(),
            'total_rooms' => HostelRoom::count(),
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'occupancy_percent' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0,
            'active_allocations' => StudentHostelAllocation::where('status', 'active')->count(),
        ];

        $occupancyByHostel = Hostel::withSum('rooms as room_capacity', 'capacity')
            ->withSum('rooms as room_occupied', 'current_occupancy')
            ->get()
            ->map(function ($h) {
                $cap = $h->room_capacity ?? 0;
                $occ = $h->room_occupied ?? 0;
                $h->occupancy_percent = $cap > 0 ? round(($occ / $cap) * 100, 1) : 0;
                return $h;
            });

        $currentMonth = Carbon::now()->format('Y-m');
        $monthPayments = StudentHostelFeePayment::where('month', $currentMonth)->get();

        $revenue = [
            'expected' => $monthPayments->sum('amount'),
            'collected' => $monthPayments->sum('paid_amount'),
            'pending' => $monthPayments->sum(fn ($p) => $p->amount - $p->paid_amount),
            'overdue_count' => StudentHostelFeePayment::whereIn('status', ['pending', 'partial'])
                ->where('due_date', '<', Carbon::today())->count(),
        ];

        return view('admin.hostel-dashboard.index', compact('stats', 'occupancyByHostel', 'revenue', 'currentMonth'));
    }
}
