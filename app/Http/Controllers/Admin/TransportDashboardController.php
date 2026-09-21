<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\TransportRoute;
use App\Models\StudentTransport;
use App\Models\StudentTransportFeePayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransportDashboardController extends Controller
{
    public function index()
    {
        $vehicleStatusCounts = Vehicle::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $stats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => $vehicleStatusCounts->get('active', 0),
            'maintenance_vehicles' => $vehicleStatusCounts->get('maintenance', 0),
            'inactive_vehicles' => $vehicleStatusCounts->get('inactive', 0),
            'total_drivers' => Driver::where('is_active', true)->count(),
            'total_routes' => TransportRoute::where('is_active', true)->count(),
            'active_assignments' => StudentTransport::where('status', 'active')->count(),
        ];

        $currentMonth = Carbon::now()->format('Y-m');
        $monthPayments = StudentTransportFeePayment::where('month', $currentMonth)->get();

        $revenue = [
            'expected' => $monthPayments->sum('amount'),
            'collected' => $monthPayments->sum('paid_amount'),
            'pending' => $monthPayments->sum(fn ($p) => $p->amount - $p->paid_amount),
            'overdue_count' => StudentTransportFeePayment::whereIn('status', ['pending', 'partial'])
                ->where('due_date', '<', Carbon::today())->count(),
        ];

        $revenueByRoute = TransportRoute::withCount(['studentTransports as active_students' => function ($q) {
                $q->where('status', 'active');
            }])
            ->having('active_students', '>', 0)
            ->orderByDesc('active_students')
            ->limit(10)
            ->get();

        return view('admin.transport-dashboard.index', compact('stats', 'revenue', 'revenueByRoute', 'currentMonth'));
    }
}
