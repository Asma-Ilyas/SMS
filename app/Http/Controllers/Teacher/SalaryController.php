<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends BaseTeacherController
{
    public function index(Request $request)
    {
        $t = $this->teacher();
        $year = (string) $request->get('year', now()->format('Y'));

        $years = DB::table('salaries')->where('staff_id', $t->id)
            ->selectRaw('DISTINCT LEFT(month, 4) as y')->orderByDesc('y')->pluck('y');

        $salaries = DB::table('salaries')->where('staff_id', $t->id)
            ->where('month', 'like', $year . '%')->orderByDesc('month')->get();

        $totals = [
            'gross' => $salaries->sum(fn ($s) => $s->basic_salary + $s->allowances + $s->bonus + $s->overtime_pay + $s->attendance_bonus + $s->commission),
            'ded'   => $salaries->sum(fn ($s) => $s->deductions + $s->penalties + $s->leave_deductions + $s->tax + $s->pf_employee),
            'net'   => $salaries->sum('net_salary'),
            'paid'  => $salaries->where('payment_status', 'paid')->sum('net_salary'),
        ];

        return view('teacher.salary.index', compact('t', 'salaries', 'years', 'year', 'totals'));
    }

    public function show($id)
    {
        $t = $this->teacher();
        $salary = DB::table('salaries')->where('id', $id)->first();
        $this->ensureOwner($salary);

        return view('teacher.salary.show', compact('t', 'salary'));
    }
}
