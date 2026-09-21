<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HostelController extends BaseStudentController
{
    public function index()
    {
        $s = $this->student();
        $enabled = Schema::hasTable('student_hostel_allocations');
        $allocation = null;
        $payments = collect();
        $attendance = collect();
        $history = collect();

        if ($enabled) {
            $rows = DB::table('student_hostel_allocations as a')
                ->join('hostels as h', 'h.id', '=', 'a.hostel_id')
                ->join('hostel_rooms as r', 'r.id', '=', 'a.room_id')
                ->leftJoin('hostel_room_types as rt', 'rt.id', '=', 'r.room_type_id')
                ->leftJoin('hostel_fee_types as ft', 'ft.id', '=', 'a.hostel_fee_type_id')
                ->leftJoin('staff as w', 'w.id', '=', 'h.warden_id')
                ->where('a.student_id', $s->id)
                ->orderByDesc('a.allocation_date')
                ->select('a.*', 'h.name as hostel_name', 'h.type as hostel_type', 'h.address as hostel_address',
                         'r.room_number', 'r.floor', 'r.capacity as room_capacity', 'rt.name as room_type',
                         'ft.name as fee_name', 'ft.amount as fee_amount', 'ft.period as fee_period',
                         DB::raw("CONCAT(w.first_name,' ',w.last_name) as warden_name"), 'w.mobile as warden_phone')
                ->get();

            $allocation = $rows->firstWhere('status', 'active') ?? $rows->firstWhere('status', 'pending');
            $history    = $rows->where('status', 'vacated');
            $ids        = $rows->pluck('id');

            $payments = DB::table('student_hostel_fee_payments')
                ->whereIn('student_hostel_allocation_id', $ids)
                ->orderByDesc('due_date')->get();

            $attendance = DB::table('hostel_attendance')
                ->whereIn('student_hostel_allocation_id', $ids)
                ->orderByDesc('attendance_date')->limit(30)->get();
        }

        return view('student.hostel.index', compact('s', 'enabled', 'allocation', 'history', 'payments', 'attendance'));
    }
}
