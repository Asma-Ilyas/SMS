<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransportController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();
        $enabled = Schema::hasTable('student_transports');
        $assignments = collect();
        $stops = [];
        $payments = collect();

        if ($enabled) {
            $assignments = DB::table('student_transports as st')
                ->join('transport_routes as r', 'r.id', '=', 'st.route_id')
                ->leftJoin('route_stops as rs', 'rs.id', '=', 'st.route_stop_id')
                ->leftJoin('vehicles as v', 'v.id', '=', DB::raw('COALESCE(st.vehicle_id, r.vehicle_id)'))
                ->leftJoin('drivers as d', 'd.id', '=', DB::raw('COALESCE(v.driver_id, r.driver_id)'))
                ->leftJoin('transport_fee_types as ft', 'ft.id', '=', 'st.transport_fee_type_id')
                ->where('st.student_id', $s->id)
                ->orderByRaw("FIELD(st.status,'active','pending','inactive')")
                ->orderByDesc('st.start_date')
                ->select('st.*', 'r.name as route_name', 'r.code as route_code', 'r.start_point', 'r.end_point',
                         'rs.stop_name', 'rs.pickup_time', 'rs.drop_time',
                         'v.vehicle_number', 'v.type as vehicle_type',
                         'd.name as driver_name', 'd.phone as driver_phone',
                         'ft.name as fee_name', 'ft.amount as fee_amount', 'ft.period as fee_period')
                ->get();

            foreach ($assignments->pluck('route_id')->unique() as $routeId) {
                $stops[$routeId] = DB::table('route_stops')->where('route_id', $routeId)->orderBy('stop_order')->get();
            }

            $payments = DB::table('student_transport_fee_payments as p')
                ->join('student_transports as st', 'st.id', '=', 'p.student_transport_id')
                ->where('st.student_id', $s->id)
                ->orderByDesc('p.due_date')->select('p.*')->get();
        }

        return view('student.transport.index', compact('s', 'enabled', 'assignments', 'stops', 'payments'));
    }
}
